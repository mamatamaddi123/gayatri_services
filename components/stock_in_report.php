<?php
// Add session start at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("db.php");

// Get logged-in user details
$userId = null;
$userBranchId = null;
$userName = $_SESSION['userName'] ?? '';

if (!empty($userName)) {
    $stmt = $conn->prepare("SELECT userId, branchId FROM users WHERE userName = ?");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    if ($user_data) {
        $userId = $user_data['userId'];
        $userBranchId = $user_data['branchId'];
    } else {
        $userId = $_SESSION['userId'] ?? 1;
        $userBranchId = $_SESSION['branchId'] ?? 1;
    }
} else {
    $userId = $_SESSION['userId'] ?? 1;
    $userBranchId = $_SESSION['branchId'] ?? 1;
}

// Initialize filters
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
$date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
$supplier_filter = $_GET['supplier'] ?? '';
$item_filter = $_GET['item'] ?? '';
$document_filter = $_GET['document'] ?? '';
$branch_filter = $_GET['branch'] ?? '';
$user_filter = $_GET['user'] ?? '';
$search = $_GET['search'] ?? '';

// Pagination
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where_conditions = ["th.flag = 0"]; // Stock In flag
$params = [];
$param_types = "";

if (!empty($date_from)) {
    $where_conditions[] = "DATE(th.Trans_date) >= ?";
    $params[] = $date_from;
    $param_types .= "s";
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(th.Trans_date) <= ?";
    $params[] = $date_to;
    $param_types .= "s";
}

if (!empty($supplier_filter)) {
    $where_conditions[] = "th.custId = ?";
    $params[] = $supplier_filter;
    $param_types .= "i";
}

if (!empty($item_filter)) {
    $where_conditions[] = "tl.item_Code = ?";
    $params[] = $item_filter;
    $param_types .= "s";
}

if (!empty($document_filter)) {
    $where_conditions[] = "th.Trans_Docs_No LIKE ?";
    $params[] = "%$document_filter%";
    $param_types .= "s";
}

if (!empty($branch_filter)) {
    $where_conditions[] = "th.branchId = ?";
    $params[] = $branch_filter;
    $param_types .= "i";
}

if (!empty($user_filter)) {
    $where_conditions[] = "th.userId = ?";
    $params[] = $user_filter;
    $param_types .= "i";
}

if (!empty($search)) {
    $where_conditions[] = "(th.Trans_Docs_No LIKE ? OR s.supName LIKE ? OR i.item_Name LIKE ? OR i.item_Code LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $param_types .= "ssss";
}

$where_clause = implode(" AND ", $where_conditions);

// Main query for data - Group by document
$main_query = "
    SELECT 
        th.Trans_Docs_No,
        th.Trans_date,
        th.remarks,
        s.supName as supplier_name,
        s.supAdd as supplier_address,
        b.branchName,
        u.userName,
        COUNT(tl.item_Code) as total_items,
        SUM(tl.qty) as total_quantity,
        SUM(tl.total) as total_value
    FROM trans_head th
    LEFT JOIN trans_line tl ON th.Trans_Docs_No = tl.Trans_Docs_No AND tl.flag = 0
    LEFT JOIN suppliers s ON th.custId = s.supId
    LEFT JOIN items i ON tl.item_Code = i.item_Code
    LEFT JOIN branch b ON th.branchId = b.branchId
    LEFT JOIN users u ON th.userId = u.userId
    WHERE $where_clause
    GROUP BY th.Trans_Docs_No, th.Trans_date, th.remarks, s.supName, s.supAdd, b.branchName, u.userName
    ORDER BY th.Trans_date DESC, th.Trans_Docs_No DESC
    LIMIT $limit OFFSET $offset
";

// Count query for pagination
$count_query = "
    SELECT COUNT(DISTINCT th.Trans_Docs_No) as total
    FROM trans_head th
    LEFT JOIN trans_line tl ON th.Trans_Docs_No = tl.Trans_Docs_No AND tl.flag = 0
    LEFT JOIN suppliers s ON th.custId = s.supId
    LEFT JOIN items i ON tl.item_Code = i.item_Code
    LEFT JOIN branch b ON th.branchId = b.branchId
    LEFT JOIN users u ON th.userId = u.userId
    WHERE $where_clause
";

// Statistics query
$stats_query = "
    SELECT 
        COUNT(DISTINCT th.Trans_Docs_No) as total_documents,
        COUNT(tl.item_Code) as total_items,
        SUM(tl.qty) as total_quantity,
        SUM(tl.total) as total_value,
        AVG(tl.rate) as avg_price,
        COUNT(DISTINCT th.custId) as unique_suppliers,
        COUNT(DISTINCT tl.item_Code) as unique_items
    FROM trans_head th
    LEFT JOIN trans_line tl ON th.Trans_Docs_No = tl.Trans_Docs_No AND tl.flag = 0
    LEFT JOIN suppliers s ON th.custId = s.supId
    LEFT JOIN items i ON tl.item_Code = i.item_Code
    WHERE $where_clause
";

// Execute queries
try {
    $stmt = $conn->prepare($main_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} catch (Exception $e) {
    $data = [];
    $error_message = "Error loading stock in data: " . $e->getMessage();
}

// Get total count
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$count_result = $stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Get statistics
$stmt = $conn->prepare($stats_query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$stats_result = $stmt->get_result();
$stats = $stats_result->fetch_assoc();

// Get dropdown data
$suppliers = [];
$result = $conn->query("SELECT supId, supName FROM suppliers ORDER BY supName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

$items = [];
$result = $conn->query("SELECT DISTINCT item_Code, item_Name FROM items ORDER BY item_Code ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$branches = [];
$result = $conn->query("SELECT branchId, branchName FROM branch ORDER BY branchName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }
}

$users = [];
$result = $conn->query("SELECT userId, userName FROM users ORDER BY userName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<title>Stock In Report</title>
<link rel="stylesheet" href="stock_in.css">
<link rel="stylesheet" href="crud.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body>

<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <h1>📈 Stock In Report</h1>
        <div class="report-actions">
            <button onclick="exportToCSV()" class="export-btn">📊 Export CSV</button>
            <button onclick="printReport()" class="print-btn">🖨️ Print</button>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-summary">
        <div class="stats-line">
            <span class="stat-item">📄 <strong><?= number_format($stats['total_documents'] ?? 0) ?></strong> Documents</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">📦 <strong><?= number_format($stats['total_items'] ?? 0) ?></strong> Items</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">🔢 <strong><?= number_format($stats['total_quantity'] ?? 0) ?></strong> Quantity</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">💰 <strong>₹<?= number_format($stats['total_value'] ?? 0, 2) ?></strong> Value</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">🏪 <strong><?= number_format($stats['unique_suppliers'] ?? 0) ?></strong> Suppliers</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">📋 <strong><?= number_format($stats['unique_items'] ?? 0) ?></strong> Unique Items</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="filter-form" id="filterForm">
            <input type="hidden" name="page" value="stock_in_report">
            
            <div class="filter-row">
                <div class="filter-group">
                    <label>Date From:</label>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" class="filter-input">
                </div>
                <div class="filter-group">
                    <label>Date To:</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" class="filter-input">
                </div>
                <div class="filter-group">
                    <label>Supplier:</label>
                    <select name="supplier" class="filter-select">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['supId'] ?>" <?= ($supplier_filter == $supplier['supId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['supName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Item:</label>
                    <select name="item" class="filter-select">
                        <option value="">All Items</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item['item_Code'] ?>" <?= ($item_filter == $item['item_Code']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['item_Code'] . ' - ' . $item['item_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label>Branch:</label>
                    <select name="branch" class="filter-select">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['branchId'] ?>" <?= ($branch_filter == $branch['branchId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['branchName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>User:</label>
                    <select name="user" class="filter-select">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['userId'] ?>" <?= ($user_filter == $user['userId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['userName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Document No:</label>
                    <input type="text" name="document" value="<?= htmlspecialchars($document_filter) ?>" placeholder="Document number..." class="filter-input">
                </div>
                <div class="filter-group">
                    <label>Search:</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search all fields..." class="filter-input">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="filter-btn">🔍 Apply Filters</button>
                <button type="button" onclick="clearFilters()" class="clear-btn">🗑️ Clear</button>
                <button type="button" onclick="toggleFilters()" class="toggle-btn" id="toggleBtn">📋 Hide Filters</button>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        <div class="results-count">
            Showing <?= number_format(count($data)) ?> of <?= number_format($total_records) ?> records
            <?php if (!empty($search) || !empty($supplier_filter) || !empty($item_filter)): ?>
                <span class="filter-indicator">(Filtered)</span>
            <?php endif; ?>
        </div>
        <div class="pagination-info">
            Page <?= $page ?> of <?= $total_pages ?>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <table class="report-table" id="reportTable">
            <thead>
                <tr>
                    <th>Document No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Total Qty</th>
                    <th>Total Value</th>
                    <th>Branch</th>
                    <th>User</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="10" class="no-data">No records found matching your criteria</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td class="doc-no"><?= htmlspecialchars($row['Trans_Docs_No']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['Trans_date'])) ?></td>
                            <td><?= htmlspecialchars($row['supplier_name'] ?? 'N/A') ?></td>
                            <td class="text-center"><?= number_format($row['total_items']) ?></td>
                            <td class="text-right"><?= number_format($row['total_quantity']) ?></td>
                            <td class="text-right">₹<?= number_format($row['total_value'], 2) ?></td>
                            <td><?= htmlspecialchars($row['branchName'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['userName'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['remarks']) ?></td>
                            <td class="text-center">
                                <button type="button" class="view-btn" onclick="viewDocumentItems('<?= htmlspecialchars($row['Trans_Docs_No']) ?>')" title="View Items">
                                    👁️ View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['p' => 1])) ?>" class="page-btn">First</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" class="page-btn">Previous</a>
            <?php endif; ?>
            
            <?php
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
                   class="page-btn <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" class="page-btn">Next</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['p' => $total_pages])) ?>" class="page-btn">Last</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Document Items Modal -->
<div id="documentItemsModal" class="modal">
    <div class="modal-content document-modal">
        <div class="modal-header">
            <h3>Document Items - <span id="modalDocNo"></span></h3>
            <span class="close" onclick="closeDocumentModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="documentItemsContent">
                <div class="loading">Loading items...</div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle filters visibility
function toggleFilters() {
    const filterSection = document.querySelector('.filter-section');
    const toggleBtn = document.getElementById('toggleBtn');
    
    if (filterSection.style.display === 'none') {
        filterSection.style.display = 'block';
        toggleBtn.textContent = '📋 Hide Filters';
    } else {
        filterSection.style.display = 'none';
        toggleBtn.textContent = '📋 Show Filters';
    }
}

// Clear all filters
function clearFilters() {
    window.location.href = 'admin.php?page=stock_in_report';
}

// Export to CSV
function exportToCSV() {
    const table = document.getElementById('reportTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cols = row.querySelectorAll('td, th');
        let csvRow = [];
        
        for (let j = 0; j < cols.length; j++) {
            csvRow.push('"' + cols[j].textContent.replace(/"/g, '""') + '"');
        }
        csv.push(csvRow.join(','));
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_in_report_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Print report
function printReport() {
    window.print();
}

// View document items
function viewDocumentItems(docNo) {
    document.getElementById('modalDocNo').textContent = docNo;
    document.getElementById('documentItemsModal').style.display = 'block';
    
    // Load items via AJAX
    fetch('components/get_document_items.php?doc_no=' + encodeURIComponent(docNo) + '&flag=0')
        .then(response => response.text())
        .then(data => {
            document.getElementById('documentItemsContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('documentItemsContent').innerHTML = '<div class="error">Error loading items: ' + error.message + '</div>';
        });
}

// Close modal
function closeDocumentModal() {
    document.getElementById('documentItemsModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('documentItemsModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Auto-submit form on filter change
$(document).ready(function() {
    $('.filter-select, .filter-input').on('change', function() {
        // Optional: Auto-submit on change
        // $('#filterForm').submit();
    });
});
</script>

<style>
.report-container {
    padding: 20px;
    max-width: 100%;
    overflow-x: auto;
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
}

.report-header h1 {
    margin: 0;
    color: #333;
    font-size: 24px;
}

.report-actions {
    display: flex;
    gap: 10px;
}

.export-btn, .print-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.export-btn {
    background-color: #28a745;
    color: white;
}

.export-btn:hover {
    background-color: #218838;
}

.print-btn {
    background-color: #6c757d;
    color: white;
}

.print-btn:hover {
    background-color: #5a6268;
}

.stats-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stats-line {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 14px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

.stat-separator {
    color: rgba(255, 255, 255, 0.5);
    font-weight: bold;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
    font-size: 12px;
}

.filter-input, .filter-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-start;
}

.filter-btn, .clear-btn, .toggle-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.filter-btn {
    background-color: #007bff;
    color: white;
}

.filter-btn:hover {
    background-color: #0056b3;
}

.clear-btn {
    background-color: #dc3545;
    color: white;
}

.clear-btn:hover {
    background-color: #c82333;
}

.toggle-btn {
    background-color: #6c757d;
    color: white;
}

.toggle-btn:hover {
    background-color: #5a6268;
}

.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background: #e9ecef;
    border-radius: 4px;
}

.filter-indicator {
    color: #007bff;
    font-weight: bold;
}

.table-container {
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 20px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.report-table th {
    background-color: #343a40;
    color: white;
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 10;
}

.report-table td {
    padding: 10px 8px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: top;
}

.report-table tbody tr:hover {
    background-color: #f8f9fa;
}

.text-right {
    text-align: right;
}

.doc-no {
    font-weight: 600;
    color: #007bff;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.page-btn {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    color: #007bff;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s;
}

.page-btn:hover {
    background-color: #007bff;
    color: white;
}

.page-btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.view-btn {
    background-color: #17a2b8;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    transition: background-color 0.3s;
}

.view-btn:hover {
    background-color: #138496;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content.document-modal {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: none;
    width: 90%;
    max-width: 1000px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background-color: #007bff;
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
}

.modal-header .close {
    color: white;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.modal-header .close:hover {
    opacity: 0.7;
}

.modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.error {
    text-align: center;
    padding: 40px;
    color: #dc3545;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}

.text-center {
    text-align: center;
}

@media print {
    .filter-section, .report-actions, .pagination {
        display: none;
    }
    
    .report-container {
        padding: 0;
    }
    
    .report-table {
        font-size: 10px;
    }
    
    .stats-summary {
        background: #f8f9fa !important;
        color: #333 !important;
        border: 1px solid #ddd;
    }
}

@media (max-width: 768px) {
    .filter-row {
        grid-template-columns: 1fr;
    }
    
    .stats-line {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .stat-separator {
        display: none;
    }
    
    .report-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .results-info {
        flex-direction: column;
        gap: 5px;
        align-items: flex-start;
    }
}
</style>

</body>
</html>