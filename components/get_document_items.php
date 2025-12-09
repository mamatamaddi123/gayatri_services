<?php
// Add session start at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
$root_path = dirname(dirname(__FILE__));
$db_path = $root_path . '/db.php';

if (file_exists($db_path)) {
    include($db_path);
} else {
    // Try alternative paths
    $alt_paths = ['../db.php', '../../db.php', dirname(__FILE__) . '/../db.php'];
    $included = false;
    foreach ($alt_paths as $path) {
        if (file_exists($path)) {
            include($path);
            $included = true;
            break;
        }
    }
    if (!$included) {
        echo '<div class="error">Database connection file not found</div>';
        exit;
    }
}

// Check if database connection exists
if (!isset($conn) || $conn === null) {
    echo '<div class="error">Database connection failed</div>';
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo '<div class="error">Access denied</div>';
    exit;
}

$doc_no = $_GET['doc_no'] ?? '';
$flag = $_GET['flag'] ?? '0'; // 0 for stock in, 1 for stock out

if (empty($doc_no)) {
    echo '<div class="error">Document number is required</div>';
    exit;
}

try {
    // Get document header info
    $header_query = "
        SELECT 
            th.Trans_Docs_No,
            th.Trans_date,
            th.remarks,
            CASE 
                WHEN th.flag = 0 THEN s.supName
                ELSE c.custName
            END as party_name,
            CASE 
                WHEN th.flag = 0 THEN s.supAdd
                ELSE c.custAdd
            END as party_address,
            CASE 
                WHEN th.flag = 0 THEN s.phoneNo
                ELSE c.cust_phoneNo
            END as party_phone,
            b.branchName,
            u.userName
        FROM trans_head th
        LEFT JOIN suppliers s ON th.custId = s.supId AND th.flag = 0
        LEFT JOIN customers c ON th.custId = c.custId AND th.flag = 1
        LEFT JOIN branch b ON th.branchId = b.branchId
        LEFT JOIN users u ON th.userId = u.userId
        WHERE th.Trans_Docs_No = ? AND th.flag = ?
    ";
    
    $stmt = $conn->prepare($header_query);
    $stmt->bind_param("ss", $doc_no, $flag);
    $stmt->execute();
    $header_result = $stmt->get_result();
    $header = $header_result->fetch_assoc();
    
    if (!$header) {
        echo '<div class="error">Document not found</div>';
        exit;
    }
    
    // Get document items using item_Id for accurate fetching
    $items_query = "
        SELECT 
            tl.item_Id,
            tl.item_Code,
            i.item_Name,
            tl.qty,
            tl.rate,
            tl.total,
            c.catgName as category_name,
            b.brandName as brand_name,
            u.UOM_Name as uom_name,
            i.price as current_price,
            i.rack_No as rack_number,
            i.stat as item_status
        FROM trans_line tl
        LEFT JOIN items i ON tl.item_Id = i.item_Id
        LEFT JOIN category c ON i.catgId = c.catgId
        LEFT JOIN brand b ON i.brandId = b.brandId
        LEFT JOIN uom u ON i.UOM_Id = u.UOM_Id
        WHERE tl.Trans_Docs_No = ? AND tl.flag = ?
        ORDER BY tl.item_Id, tl.item_Code
    ";
    
    $stmt = $conn->prepare($items_query);
    $stmt->bind_param("ss", $doc_no, $flag);
    $stmt->execute();
    $items_result = $stmt->get_result();
    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
    
    // Calculate totals
    $total_items = count($items);
    $total_qty = array_sum(array_column($items, 'qty'));
    $total_value = array_sum(array_column($items, 'total'));
    
    $party_label = ($flag == '0') ? 'Supplier' : 'Customer';
    $doc_type = ($flag == '0') ? 'Stock In' : 'Stock Out';
    
    // Debug information (uncomment to enable)
    // echo "<!-- Debug: Found " . count($items) . " items using item_Id join -->";
    
    ?>
    
    <!-- Document Header -->
    <div class="document-header">
        <div class="header-row">
            <div class="header-group">
                <strong>Document Type:</strong> <?= $doc_type ?>
            </div>
            <div class="header-group">
                <strong>Date:</strong> <?= date('d/m/Y', strtotime($header['Trans_date'])) ?>
            </div>
            <div class="header-group">
                <strong>Branch:</strong> <?= htmlspecialchars($header['branchName'] ?? 'N/A') ?>
            </div>
            <div class="header-group">
                <strong>User:</strong> <?= htmlspecialchars($header['userName'] ?? 'N/A') ?>
            </div>
        </div>
        <div class="header-row">
            <div class="header-group">
                <strong><?= $party_label ?>:</strong> <?= htmlspecialchars($header['party_name'] ?? 'N/A') ?>
            </div>
            <div class="header-group">
                <strong>Address:</strong> <?= htmlspecialchars($header['party_address'] ?? 'N/A') ?>
            </div>
            <div class="header-group">
                <strong>Phone:</strong> <?= htmlspecialchars($header['party_phone'] ?? 'N/A') ?>
            </div>
        </div>
        <?php if (!empty($header['remarks'])): ?>
        <div class="header-row">
            <div class="header-group full-width">
                <strong>Remarks:</strong> <?= htmlspecialchars($header['remarks']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Document Summary -->
    <div class="document-summary">
        <div class="summary-item">
            <span class="summary-label">Total Items:</span>
            <span class="summary-value"><?= number_format($total_items) ?></span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Quantity:</span>
            <span class="summary-value"><?= number_format($total_qty) ?></span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Value:</span>
            <span class="summary-value">₹<?= number_format($total_value, 2) ?></span>
        </div>
    </div>
    
    <!-- Items Table -->
    <div class="items-table-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Total</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="10" class="no-items">No items found for this document</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php 
                        // Calculate price difference
                        $rate = floatval($item['rate']);
                        $current_price = floatval($item['current_price'] ?? 0);
                        $price_diff = $current_price - $rate;
                        $price_diff_class = '';
                        if ($price_diff > 0) {
                            $price_diff_class = 'price-increase';
                        } elseif ($price_diff < 0) {
                            $price_diff_class = 'price-decrease';
                        }
                        ?>
                        <tr <?= empty($item['item_Name']) ? 'class="missing-item"' : '' ?>>
                            <td class="item-id"><?= htmlspecialchars($item['item_Id']) ?></td>
                            <td class="item-code"><?= htmlspecialchars($item['item_Code']) ?></td>
                            <td>
                                <?php if (empty($item['item_Name'])): ?>
                                    <span class="missing-item-text">⚠️ Item not found in master</span>
                                    <br><small class="text-muted">Item may have been deleted</small>
                                <?php else: ?>
                                    <?= htmlspecialchars($item['item_Name']) ?>
                                    <?php if (!empty($item['rack_number'])): ?>
                                        <br><small class="rack-info">📍 Rack: <?= htmlspecialchars($item['rack_number']) ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['category_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($item['brand_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($item['uom_name'] ?? 'N/A') ?></td>
                            <td class="text-right"><?= number_format($item['qty']) ?></td>
                            <td class="text-right">₹<?= number_format($item['rate'], 2) ?></td>
                            <td class="text-right">₹<?= number_format($item['total'], 2) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <style>
    .document-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .header-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 10px;
    }
    
    .header-row:last-child {
        margin-bottom: 0;
    }
    
    .header-group {
        flex: 1;
        min-width: 200px;
    }
    
    .header-group.full-width {
        flex: 100%;
    }
    
    .document-summary {
        display: flex;
        justify-content: space-around;
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .summary-item {
        text-align: center;
    }
    
    .summary-label {
        display: block;
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .summary-value {
        display: block;
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
    }
    
    .items-table-container {
        overflow-x: auto;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .items-table th {
        background-color: #343a40;
        color: white;
        padding: 12px 8px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .items-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: top;
    }
    
    .items-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .text-right {
        text-align: right;
    }
    
    .item-id {
        font-weight: 600;
        color: #28a745;
        text-align: center;
    }
    
    .item-code {
        font-weight: 600;
        color: #007bff;
    }
    
    .no-items {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        margin: 20px;
        text-align: center;
    }
    
    .rack-info {
        color: #6c757d;
        font-style: italic;
    }
    
    .price-increase {
        color: #dc3545;
        font-weight: bold;
    }
    
    .price-decrease {
        color: #28a745;
        font-weight: bold;
    }
    
    .missing-item {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    
    .missing-item-text {
        color: #856404;
        font-weight: bold;
    }
    
    .text-muted {
        color: #6c757d;
        font-style: italic;
    }
    
    @media (max-width: 768px) {
        .header-row {
            flex-direction: column;
            gap: 10px;
        }
        
        .document-summary {
            flex-direction: column;
            gap: 15px;
        }
        
        .items-table {
            font-size: 12px;
        }
    }
    </style>
    
    <?php
    
} catch (Exception $e) {
    echo '<div class="error">Error loading document items: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>