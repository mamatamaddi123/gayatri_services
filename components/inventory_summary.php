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
$item_filter = $_GET['item'] ?? '';
$category_filter = $_GET['category'] ?? '';
$brand_filter = $_GET['brand'] ?? '';
$search = $_GET['search'] ?? '';
$show_zero_stock = isset($_GET['show_zero_stock']) ? $_GET['show_zero_stock'] : '0';

// Pagination
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Build WHERE clause for filters
$where_conditions = [];
$params = [];
$param_types = "";

if (!empty($item_filter)) {
    $where_conditions[] = "i.item_Code = ?";
    $params[] = $item_filter;
    $param_types .= "s";
}

if (!empty($category_filter)) {
    $where_conditions[] = "i.catgId = ?";
    $params[] = $category_filter;
    $param_types .= "i";
}

if (!empty($brand_filter)) {
    $where_conditions[] = "i.brandId = ?";
    $params[] = $brand_filter;
    $param_types .= "i";
}

if (!empty($search)) {
    $where_conditions[] = "(i.item_Code LIKE ? OR i.item_Name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
    $param_types .= "ss";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Main inventory query with stock calculations
$main_query = "
    SELECT 
        i.item_Code,
        i.item_Name,
        i.price as current_price,
        c.catgName as category_name,
        b.brandName as brand_name,
        u.UOM_Name as uom_name,
        
        -- Stock In calculations
        COALESCE(stock_in.total_in_qty, 0) as total_stock_in,
        COALESCE(stock_in.total_in_value, 0) as total_stock_in_value,
        COALESCE(stock_in.avg_in_price, 0) as avg_stock_in_price,
        
        -- Stock Out calculations  
        COALESCE(stock_out.total_out_qty, 0) as total_stock_out,
        COALESCE(stock_out.total_out_value, 0) as total_stock_out_value,
        COALESCE(stock_out.avg_out_price, 0) as avg_stock_out_price,
        
        -- Current stock calculation
        (COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) as current_stock,
        
        -- Current stock value (using average in price)
        ((COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) * COALESCE(stock_in.avg_in_price, i.price)) as current_stock_value
        
    FROM items i
    LEFT JOIN category c ON i.catgId = c.catgId
    LEFT JOIN brand b ON i.brandId = b.brandId  
    LEFT JOIN uom u ON i.UOM_Id = u.UOM_Id
    
    -- Stock In subquery
    LEFT JOIN (
        SELECT 
            tl.item_Code,
            SUM(tl.qty) as total_in_qty,
            SUM(tl.total) as total_in_value,
            AVG(tl.rate) as avg_in_price
        FROM trans_line tl
        INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
        WHERE th.flag = 0 AND tl.flag = 0
        GROUP BY tl.item_Code
    ) stock_in ON i.item_Code = stock_in.item_Code
    
    -- Stock Out subquery
    LEFT JOIN (
        SELECT 
            tl.item_Code,
            SUM(tl.qty) as total_out_qty,
            SUM(tl.total) as total_out_value,
            AVG(tl.rate) as avg_out_price
        FROM trans_line tl
        INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
        WHERE th.flag = 1 AND tl.flag = 1
        GROUP BY tl.item_Code
    ) stock_out ON i.item_Code = stock_out.item_Code
    
    $where_clause
";

// Add zero stock filter
if ($show_zero_stock == '0') {
    $having_clause = " HAVING current_stock > 0";
} else {
    $having_clause = "";
}

$main_query .= $having_clause . " ORDER BY i.item_Code ASC LIMIT $limit OFFSET $offset";

// Count query
$count_query = "
    SELECT COUNT(*) as total
    FROM (
        SELECT 
            i.item_Code,
            (COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) as current_stock
        FROM items i
        LEFT JOIN category c ON i.catgId = c.catgId
        LEFT JOIN brand b ON i.brandId = b.brandId
        LEFT JOIN (
            SELECT tl.item_Code, SUM(tl.qty) as total_in_qty
            FROM trans_line tl
            INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
            WHERE th.flag = 0 AND tl.flag = 0
            GROUP BY tl.item_Code
        ) stock_in ON i.item_Code = stock_in.item_Code
        LEFT JOIN (
            SELECT tl.item_Code, SUM(tl.qty) as total_out_qty
            FROM trans_line tl
            INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
            WHERE th.flag = 1 AND tl.flag = 1
            GROUP BY tl.item_Code
        ) stock_out ON i.item_Code = stock_out.item_Code
        $where_clause
        $having_clause
    ) as counted_items
";

// Overall statistics query
$stats_query = "
    SELECT 
        COUNT(DISTINCT i.item_Code) as total_items,
        SUM(COALESCE(stock_in.total_in_qty, 0)) as total_stock_in_qty,
        SUM(COALESCE(stock_out.total_out_qty, 0)) as total_stock_out_qty,
        SUM(COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) as total_current_stock,
        SUM((COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) * COALESCE(stock_in.avg_in_price, i.price)) as total_stock_value,
        COUNT(CASE WHEN (COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) = 0 THEN 1 END) as zero_stock_items,
        COUNT(CASE WHEN (COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) < 10 AND (COALESCE(stock_in.total_in_qty, 0) - COALESCE(stock_out.total_out_qty, 0)) > 0 THEN 1 END) as low_stock_items
    FROM items i
    LEFT JOIN (
        SELECT tl.item_Code, SUM(tl.qty) as total_in_qty, AVG(tl.rate) as avg_in_price
        FROM trans_line tl
        INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
        WHERE th.flag = 0 AND tl.flag = 0
        GROUP BY tl.item_Code
    ) stock_in ON i.item_Code = stock_in.item_Code
    LEFT JOIN (
        SELECT tl.item_Code, SUM(tl.qty) as total_out_qty
        FROM trans_line tl
        INNER JOIN trans_head th ON tl.Trans_Docs_No = th.Trans_Docs_No
        WHERE th.flag = 1 AND tl.flag = 1
        GROUP BY tl.item_Code
    ) stock_out ON i.item_Code = stock_out.item_Code
";

// Execute queries
try {
    $stmt = $conn->prepare($main_query);
    if ($stmt === false) {
        throw new Exception("Failed to prepare main query: " . $conn->error);
    }
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
    $error_message = "Error loading inventory data: " . $e->getMessage();
}

// Get total count
try {
    $stmt = $conn->prepare($count_query);
    if ($stmt === false) {
        throw new Exception("Failed to prepare count query: " . $conn->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $count_result = $stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'] ?? 0;
    $total_pages = ceil($total_records / $limit);
} catch (Exception $e) {
    $total_records = 0;
    $total_pages = 0;
}

// Get statistics
try {
    $stmt = $conn->prepare($stats_query);
    if ($stmt === false) {
        throw new Exception("Failed to prepare stats query: " . $conn->error);
    }
    $stmt->execute();
    $stats_result = $stmt->get_result();
    $stats = $stats_result->fetch_assoc();
} catch (Exception $e) {
    $stats = [
        'total_items' => 0,
        'total_stock_in_qty' => 0,
        'total_stock_out_qty' => 0,
        'total_current_stock' => 0,
        'total_stock_value' => 0,
        'zero_stock_items' => 0,
        'low_stock_items' => 0
    ];
}

// Get dropdown data
$items = [];
$result = $conn->query("SELECT DISTINCT item_Code, item_Name FROM items ORDER BY item_Code ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$categories = [];
$result = $conn->query("SELECT catgId, catgName FROM category ORDER BY catgName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$brands = [];
$result = $conn->query("SELECT brandId, brandName FROM brand ORDER BY brandName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}
?>

<title>Inventory Summary</title>
<link rel="stylesheet" href="stock_in.css">
<link rel="stylesheet" href="crud.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body>

<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <h1>📊 Inventory Summary</h1>
        <div class="report-actions">
            <button onclick="exportToCSV()" class="export-btn">📊 Export CSV</button>
            <button onclick="printReport()" class="print-btn">🖨️ Print</button>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-summary">
        <div class="stats-line">
            <span class="stat-item">📦 <strong><?= number_format($stats['total_items'] ?? 0) ?></strong> Items</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">📈 <strong><?= number_format($stats['total_stock_in_qty'] ?? 0) ?></strong> Stock In</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">📉 <strong><?= number_format($stats['total_stock_out_qty'] ?? 0) ?></strong> Stock Out</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">📋 <strong><?= number_format($stats['total_current_stock'] ?? 0) ?></strong> Current Stock</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">💰 <strong>₹<?= number_format($stats['total_stock_value'] ?? 0, 2) ?></strong> Value</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">⚠️ <strong><?= number_format($stats['zero_stock_items'] ?? 0) ?></strong> Zero Stock</span>
            <span class="stat-separator">|</span>
            <span class="stat-item">🔔 <strong><?= number_format($stats['low_stock_items'] ?? 0) ?></strong> Low Stock</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="filter-form" id="filterForm">
            <input type="hidden" name="page" value="inventory_summary">
            
            <div class="filter-row">
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
                <div class="filter-group">
                    <label>Category:</label>
                    <select name="category" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['catgId'] ?>" <?= ($category_filter == $category['catgId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['catgName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Brand:</label>
                    <select name="brand" class="filter-select">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand['brandId'] ?>" <?= ($brand_filter == $brand['brandId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand['brandName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search:</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search items..." class="filter-input">
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label>
                        <input type="checkbox" name="show_zero_stock" value="1" <?= ($show_zero_stock == '1') ? 'checked' : '' ?>>
                        Show Zero Stock Items
                    </label>
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="filter-btn">🔍 Apply Filters</button>
                <button type="button" onclick="clearFilters()" class="clear-btn">🗑️ Clear</button>
                <button type="button" onclick="toggleFilters()" class="toggle-btn" id="toggleBtn">📋 Hide Filters</button>
            </div>
        </form>
    </div>

    <!-- Error Message -->
    <?php if (isset($error_message)): ?>
        <div class="error-message">
            <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- Results Info -->
    <div class="results-info">
        <div class="results-count">
            Showing <?= number_format(count($data)) ?> of <?= number_format($total_records) ?> items
            <?php if (!empty($search) || !empty($item_filter) || !empty($category_filter)): ?>
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
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>UOM</th>
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Current Stock</th>
                    <th>Avg In Price</th>
                    <th>Current Price</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="12" class="no-data">No items found matching your criteria</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $row): ?>
                        <?php 
                        $current_stock = $row['current_stock'];
                        $status_class = '';
                        $status_text = 'In Stock';
                        
                        if ($current_stock == 0) {
                            $status_class = 'status-out';
                            $status_text = 'Out of Stock';
                        } elseif ($current_stock < 10) {
                            $status_class = 'status-low';
                            $status_text = 'Low Stock';
                        } else {
                            $status_class = 'status-good';
                            $status_text = 'Good Stock';
                        }
                        ?>
                        <tr class="<?= $status_class ?>">
                            <td class="item-code"><?= htmlspecialchars($row['item_Code']) ?></td>
                            <td><?= htmlspecialchars($row['item_Name']) ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['brand_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['uom_name'] ?? 'N/A') ?></td>
                            <td class="text-right"><?= number_format($row['total_stock_in']) ?></td>
                            <td class="text-right"><?= number_format($row['total_stock_out']) ?></td>
                            <td class="text-right stock-qty"><?= number_format($current_stock) ?></td>
                            <td class="text-right">₹<?= number_format($row['avg_stock_in_price'], 2) ?></td>
                            <td class="text-right">₹<?= number_format($row['current_price'], 2) ?></td>
                            <td class="text-right">₹<?= number_format($row['current_stock_value'], 2) ?></td>
                            <td><span class="status-badge <?= $status_class ?>"><?= $status_text ?></span></td>
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
    window.location.href = 'admin.php?page=inventory_summary';
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
    a.download = 'inventory_summary_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Print report
function printReport() {
    window.print();
}
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
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 15px;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    margin-bottom: 20px;
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
    background-color: #17a2b8;
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

.item-code {
    font-weight: 600;
    color: #007bff;
}

.stock-qty {
    font-weight: bold;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-good {
    background-color: #d4edda;
    color: #155724;
}

.status-low {
    background-color: #fff3cd;
    color: #856404;
}

.status-out {
    background-color: #f8d7da;
    color: #721c24;
}

.status-out .stock-qty {
    color: #dc3545;
}

.status-low .stock-qty {
    color: #ffc107;
}

.status-good .stock-qty {
    color: #28a745;
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