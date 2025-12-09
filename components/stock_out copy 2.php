<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection (same as stock_in.php)
$servername = "mysql5047.site4now.net";
$database = "db_a26f8d_gayatri";
$username = "a26f8d_gayatri";
$password = "Gayatri@2025";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// State
$edit_mode = false;
$editing_doc_no = '';
$existing_doc_data = null;

// Always start fresh on plain GET (e.g., sidebar navigation)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $_SESSION['so_edit_mode'] = false;
    unset($_SESSION['so_editing_doc_no']);
    $_SESSION['so_added_items'] = [];
    $_SESSION['so_form_data'] = [
        'customer' => '',
        'item_code' => '',
        'quantity' => '',
        'price' => '',
        'remarks' => ''
    ];
    $_SESSION['so_last_post'] = [];
}

// Initialize session stores
if (!isset($_SESSION['so_added_items'])) {
    $_SESSION['so_added_items'] = [];
}
if (!isset($_SESSION['so_form_data'])) {
    $_SESSION['so_form_data'] = [
        'customer' => '',
        'item_code' => '',
        'quantity' => '',
        'price' => '',
        'remarks' => ''
    ];
}
if (!isset($_SESSION['so_last_post'])) {
    $_SESSION['so_last_post'] = [];
}

// Restore edit mode from session
if (isset($_SESSION['so_edit_mode']) && $_SESSION['so_edit_mode'] === true) {
    $edit_mode = true;
    $editing_doc_no = $_SESSION['so_editing_doc_no'] ?? '';
}

// Search existing document
if (isset($_POST['search_document'])) {
    $doc_no = trim($_POST['doc_no'] ?? '');
    if ($doc_no !== '') {
        $stmt = $conn->prepare("SELECT * FROM Trans_Head WHERE Trans_Docs_No = ? AND flag = 1");
        $stmt->execute([$doc_no]);
        $existing_doc_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_doc_data) {
            $edit_mode = true;
            $editing_doc_no = $doc_no;
            $_SESSION['so_edit_mode'] = true;
            $_SESSION['so_editing_doc_no'] = $doc_no;

            // Load items
            $stmt = $conn->prepare("SELECT * FROM Trans_Line WHERE Trans_Docs_No = ? AND flag = 1");
            $stmt->execute([$doc_no]);
            $existing_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $_SESSION['so_added_items'] = [];
            foreach ($existing_items as $item) {
                $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
                $stmt->execute([$item['item_Code']]);
                $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $item_name = $item_data ? $item_data['item_Name'] : '';
                $_SESSION['so_added_items'][] = [
                    'item_code' => $item['item_Code'],
                    'item_name' => $item_name,
                    'qty' => floatval($item['qty']),
                    'price' => floatval($item['rate']),
                    'total' => floatval($item['total'])
                ];
            }

            $_SESSION['so_form_data'] = [
                'customer' => $existing_doc_data['custId'],
                'item_code' => '',
                'quantity' => '',
                'price' => '',
                'remarks' => $existing_doc_data['remarks']
            ];

            $_SESSION['so_success_message'] = "Document loaded for editing!";
        } else {
            $_SESSION['so_error_message'] = "Document not found!";
        }
    }
}

// Handle save / update
if (isset($_POST['save']) || isset($_POST['update_document'])) {
    $custId = $_POST['customer'] ?? ($_SESSION['so_form_data']['customer'] ?? '');
    $remarks = $_POST['remarks'] ?? ($_SESSION['so_form_data']['remarks'] ?? '');

    $_SESSION['so_form_data']['remarks'] = $remarks;

    if (!empty($_SESSION['so_added_items'])) {
        try {
            $conn->beginTransaction();

            if (isset($_POST['update_document'])) {
                // Update existing
                $docNo = $_POST['doc_no'] ?? $editing_doc_no;

                $stmt = $conn->prepare("UPDATE Trans_Head SET custId = ?, remarks = ? WHERE Trans_Docs_No = ?");
                $stmt->execute([$custId, $remarks, $docNo]);

                $stmt = $conn->prepare("DELETE FROM Trans_Line WHERE Trans_Docs_No = ?");
                $stmt->execute([$docNo]);

                foreach ($_SESSION['so_added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO Trans_Line (Trans_Docs_No, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, 1)");
                    $stmt->execute([$docNo, $item['item_code'], $item['qty'], $item['price'], $item['total']]);
                }

                $success_message = "Document updated successfully! Document No: $docNo";
            } else {
                // Create new
                $userId = $_SESSION['userId'] ?? 1;
                $branchId = 1;

                $stmt = $conn->prepare("SELECT branchName FROM branch WHERE branchId = ?");
                $stmt->execute([$branchId]);
                $branch = $stmt->fetch(PDO::FETCH_ASSOC);
                $branchPrefix = "SO";
                if ($branch && isset($branch['branchName'])) {
                    $branchPrefix = strtoupper(substr($branch['branchName'], 0, 2));
                }

                $timestamp = time();
                $datePart = date('Ymd');
                $docNo = $branchPrefix . "-SO-" . $datePart . "-" . $timestamp;

                $stmt = $conn->prepare("INSERT INTO Trans_Head (Trans_Docs_No, Trans_date, custId, flag, remarks, userId, branchId) VALUES (?, NOW(), ?, 1, ?, ?, ?)");
                $stmt->execute([$docNo, $custId, $remarks, $userId, $branchId]);

                foreach ($_SESSION['so_added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO Trans_Line (Trans_Docs_No, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, 1)");
                    $stmt->execute([$docNo, $item['item_code'], $item['qty'], $item['price'], $item['total']]);
                }

                $success_message = "Stock Out saved successfully! Document No: $docNo";
            }

            $conn->commit();

            // Persist success message for next load and reset state
            $_SESSION['so_success_message'] = $success_message;

            $_SESSION['so_added_items'] = [];
            $_SESSION['so_form_data'] = [
                'customer' => '',
                'item_code' => '',
                'quantity' => '',
                'price' => '',
                'remarks' => ''
            ];
            $_SESSION['so_last_post'] = [];
            $_SESSION['so_edit_mode'] = false;
            unset($_SESSION['so_editing_doc_no']);

            // Redirect to fresh page (new document view)
            header('Location: admin.php?page=stock_out');
            exit;

        } catch (Exception $e) {
            $conn->rollBack();
            $error_message = "Error " . ($edit_mode ? "updating" : "saving") . " document: " . $e->getMessage();
        }
    } else {
        $error_message = "Please add at least one item.";
    }
}

// Clear items
if (isset($_POST['clear'])) {
    $_SESSION['so_added_items'] = [];
}

// Fetch dropdown data
$customers = $conn->query("SELECT custId, custName, custAdd, cust_phoneNo FROM customers ORDER BY custName ASC")->fetchAll(PDO::FETCH_ASSOC);
$items = $conn->query("SELECT item_Code, item_Name, price FROM items ORDER BY item_Code ASC")->fetchAll(PDO::FETCH_ASSOC);

// Read state
$added_items = $_SESSION['so_added_items'];
$form_data = $_SESSION['so_form_data'];
$selected_customer = $form_data['customer'];
$customer_address = '';
$customer_phone = '';
$grand_total = 0;
foreach ($added_items as $it) { $grand_total += $it['total']; }

if (!empty($selected_customer)) {
    $stmt = $conn->prepare("SELECT custAdd, cust_phoneNo FROM customers WHERE custId = ?");
    $stmt->execute([$selected_customer]);
    if ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $customer_address = $c['custAdd'] ?? '';
        $customer_phone = $c['cust_phoneNo'] ?? '';
    }
}

// Handle items add/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add item
    if (isset($_POST['add_item'])) {
        $current_post = md5(serialize($_POST));
        if ($current_post !== ($_SESSION['so_last_post']['add_item'] ?? '')) {
            $_SESSION['so_last_post']['add_item'] = $current_post;

            $custId = $_POST['customer'] ?? '';
            $item_code = $_POST['item_code'];
            $qty = floatval($_POST['quantity']);
            $price = floatval($_POST['price']);
            $total = $qty * $price;

            $_SESSION['so_form_data'] = [
                'customer' => $custId,
                'item_code' => $item_code,
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price'],
                'remarks' => $form_data['remarks']
            ];
            $form_data = $_SESSION['so_form_data'];
            $selected_customer = $custId;

            if (!empty($custId)) {
                $stmt = $conn->prepare("SELECT custAdd, cust_phoneNo FROM customers WHERE custId = ?");
                $stmt->execute([$custId]);
                if ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $customer_address = $c['custAdd'] ?? '';
                    $customer_phone = $c['cust_phoneNo'] ?? '';
                }
            }

            $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
            $stmt->execute([$item_code]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $item_name = $item ? $item['item_Name'] : '';

            if ($item_name) {
                $_SESSION['so_added_items'][] = [
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $total
                ];
                $added_items = $_SESSION['so_added_items'];
                $grand_total += $total;
                $success_message = "Item added successfully!";

                $_SESSION['so_form_data']['item_code'] = '';
                $_SESSION['so_form_data']['quantity'] = '';
                $_SESSION['so_form_data']['price'] = '';
                $form_data = $_SESSION['so_form_data'];
            } else {
                $error_message = "Item not found!";
            }
        } else {
            $info_message = "Item was already added. Continuing with current items.";
        }
    }

    // Delete item
    if (isset($_POST['delete_item'])) {
        $delete_index = intval($_POST['delete_index']);
        if (isset($_SESSION['so_added_items'][$delete_index])) {
            array_splice($_SESSION['so_added_items'], $delete_index, 1);
            $added_items = $_SESSION['so_added_items'];
            $success_message = "Item deleted successfully!";
            $grand_total = 0;
            foreach ($added_items as $it) { $grand_total += $it['total']; }
        }
    }

    // Update item
    if (isset($_POST['update_item'])) {
        $update_index = intval($_POST['update_index']);
        $new_item_code = $_POST['new_item_code'];
        $new_qty = floatval($_POST['new_quantity']);
        $new_price = floatval($_POST['new_price']);

        if (isset($_SESSION['so_added_items'][$update_index])) {
            $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
            $stmt->execute([$new_item_code]);
            $new_item = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_item_name = $new_item ? $new_item['item_Name'] : $_SESSION['so_added_items'][$update_index]['item_name'];

            $_SESSION['so_added_items'][$update_index]['item_code'] = $new_item_code;
            $_SESSION['so_added_items'][$update_index]['item_name'] = $new_item_name;
            $_SESSION['so_added_items'][$update_index]['qty'] = $new_qty;
            $_SESSION['so_added_items'][$update_index]['price'] = $new_price;
            $_SESSION['so_added_items'][$update_index]['total'] = $new_qty * $new_price;

            $added_items = $_SESSION['so_added_items'];
            $grand_total = 0;
            foreach ($added_items as $it) { $grand_total += $it['total']; }
            $success_message = "Item updated successfully!";
        }
    }
}

// Messages
if (isset($_SESSION['so_success_message'])) { $success_message = $_SESSION['so_success_message']; unset($_SESSION['so_success_message']); }
if (isset($_SESSION['so_error_message'])) { $error_message = $_SESSION['so_error_message']; unset($_SESSION['so_error_message']); }
if (isset($_SESSION['so_info_message'])) { $info_message = $_SESSION['so_info_message']; unset($_SESSION['so_info_message']); }

// Prepare display variables similar to stock_in UI
$remarks = $form_data['remarks'] ?? '';

// Generate SO document number for display
if ($edit_mode) {
    $docNo = $editing_doc_no;
} else {
    $userId = $_SESSION['userId'] ?? 1;
    $branchId = 1;
    $stmt = $conn->prepare("SELECT branchName FROM branch WHERE branchId = ?");
    $stmt->execute([$branchId]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);
    $branchPrefix = "SO"; // Default prefix
    if ($branch && isset($branch['branchName'])) {
        $branchPrefix = strtoupper(substr($branch['branchName'], 0, 2));
    }
    $timestamp = time();
    $datePart = date('Ymd');
    $docNo = $branchPrefix . "-SO-" . $datePart . "-" . $timestamp;
}

// Item name for display
$current_item_name = '';
if (!empty($form_data['item_code'])) {
    $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
    $stmt->execute([$form_data['item_code']]);
    $it = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_item_name = $it ? $it['item_Name'] : '';
}

// UI
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Out Entry</title>
    <link rel="stylesheet" href="stock_in.css">
    <link rel="stylesheet" href="crud.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<!-- Notification Container -->
<div id="notification-container"></div>

<div class="main-container">
    <!-- Combined Document & Customer Header -->
    <div class="header-section">
        <!-- First Line: Document No + Date + Edit Button -->
        <div class="first-line">
            <div class="document-info">
                <div class="doc-number-group">
                    <label>Document No:</label>
                    <div class="doc-input-container">
                        <input type="text" name="doc_no" id="doc_no" value="<?= $docNo ?>" 
                               class="doc-number-input" <?= $edit_mode ? 'readonly' : '' ?>>
                        <?php if (!$edit_mode): ?>
                            <button type="button" id="enable_edit_btn" class="edit-doc-btn">Edit</button>
                            <button type="button" id="search_doc_btn" class="search-doc-btn" style="display: none;">Search</button>
                        <?php else: ?>
                            <span class="edit-mode-badge">EDIT MODE</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="doc-date-group">
                    <label>Date:</label>
                    <span class="date-value"><?= date('d/m/Y') ?></span>
                </div>
            </div>
        </div>

        <!-- Document Search Form (Hidden by default) -->
        <div id="document_search_section" style="display: none; margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
            <form method="post" id="documentSearchForm">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div style="flex: 1;">
                        <input type="text" name="doc_no" id="search_doc_no" placeholder="Enter Document No to edit" 
                               class="form-input" style="width: 100%;">
                    </div>
                    <div>
                        <button type="submit" name="search_document" class="search-doc-btn">Load Document</button>
                    </div>
                    <div>
                        <button type="button" id="cancel_search_btn" class="clear-btn">Cancel</button>
                    </div>
                </div>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                    Enter existing document number to edit previous stock out entry
                </p>
            </form>
        </div>

        <!-- Second Line: Customer Details -->
        <div class="second-line">
            <div class="supplier-details">
                <h3>Customer Details:</h3>
                <form method="post" class="supplier-form" id="customerForm">
                    <div class="supplier-fields">
                        <div class="form-group">
                            <label>Select Customer:</label>
                            <select name="customer" id="customer_select" required class="form-select">
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['custId'] ?>" 
                                            data-address="<?= htmlspecialchars($c['custAdd']) ?>"
                                            data-phone="<?= htmlspecialchars($c['cust_phoneNo']) ?>"
                                            <?= ($selected_customer == $c['custId']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($c['custName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <input type="text" id="customer_address" value="<?= htmlspecialchars($customer_address) ?>" readonly class="form-input address-field">
                        </div>
                        <div class="form-group">
                            <label>Phone:</label>
                            <input type="text" id="customer_phone" value="<?= htmlspecialchars($customer_phone) ?>" readonly class="form-input phone-field">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="doc-subtitle">Unique no with combination of branch prefix, date and timestamp</p>
    </div>

    <div class="divider"></div>

    <!-- Item Entry -->
    <div class="item-entry-section">
        <h2>Item Code:</h2>
        <form method="post" class="item-form" id="itemForm">
            <!-- Hidden field to preserve customer selection -->
            <input type="hidden" name="customer" id="hidden_customer" value="<?= $selected_customer ?>">
            
            <div class="item-form-row">
                <div class="form-group full-width">
                    <label>Select Item:</label>
                    <select id="item_code" name="item_code" required class="form-select item-dropdown">
                        <option value="">-- Select Item Code + Name + Price --</option>
                        <?php foreach ($items as $i): ?>
                            <option value="<?= $i['item_Code'] ?>" 
                                    data-name="<?= htmlspecialchars($i['item_Name']) ?>" 
                                    data-price="<?= $i['price'] ?>"
                                    <?= ($form_data['item_code'] == $i['item_Code']) ? 'selected' : '' ?>>
                                <?= $i['item_Code'] ?> - <?= htmlspecialchars($i['item_Name']) ?> - ₹<?= number_format($i['price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="item-details-row">
                <div class="form-group">
                    <label>Item Name:</label>
                    <input type="text" id="item_name" readonly class="form-input" placeholder="Item Name" 
                           value="<?= htmlspecialchars($current_item_name) ?>">
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" class="form-input" placeholder="Qty" required 
                           value="<?= htmlspecialchars($form_data['quantity']) ?>">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" id="price" step="1" class="form-input" placeholder="Price" required 
                           value="<?= htmlspecialchars($form_data['price']) ?>">
                </div>
                <div class="form-group">
                    <label>Total:</label>
                    <input type="text" id="total" readonly class="form-input" placeholder="Total" 
                           value="<?= (!empty($form_data['quantity']) && !empty($form_data['price'])) ? '₹' . number_format(floatval($form_data['quantity']) * floatval($form_data['price']), 2) : '' ?>">
                </div>
                <div class="form-group">
                    <button type="submit" name="add_item" class="add-item-btn">ADD ITEM</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Items Added Table -->
    <?php if (!empty($added_items)): ?>
    <div class="items-table-section">
        <h2>Items Added</h2>
        <div class="table-container">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <?php foreach ($added_items as $index => $item): ?>
                    <tr id="item-row-<?= $index ?>">
                        <td><?= htmlspecialchars($item['item_code']) ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td class="text-right item-qty" data-index="<?= $index ?>"><?= $item['qty'] ?></td>
                        <td class="text-right item-price" data-index="<?= $index ?>">₹<?= number_format($item['price'], 2) ?></td>
                        <td class="text-right item-total" data-index="<?= $index ?>">₹<?= number_format($item['total'], 2) ?></td>
                        <td>
                            <button type="button" class="edit-item-btn" onclick="enableEdit(<?= $index ?>)" data-index="<?= $index ?>">Edit</button>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="delete_index" value="<?= $index ?>">
                                <button type="submit" name="delete_item" class="delete-item-btn" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="grand-total-section">
            <div class="grand-total">
                <strong>Grand Total: ₹<span id="grand-total-amount"><?= number_format($grand_total, 2) ?></span></strong>
            </div>
        </div>
        
        <div class="action-buttons">
            <form method="post" class="action-form">
                <input type="hidden" name="customer" id="action_customer" value="<?= $selected_customer ?>">
                <input type="hidden" name="remarks" value="<?= htmlspecialchars($remarks) ?>">
                <input type="hidden" name="doc_no" value="<?= $docNo ?>">
                
                <?php if ($edit_mode): ?>
                    <button type="submit" name="update_document" class="save-btn">UPDATE DOCUMENT</button>
                <?php else: ?>
                    <button type="submit" name="save" class="save-btn">SAVE STOCK OUT</button>
                <?php endif; ?>
                
                <button type="submit" name="clear" class="clear-btn">CLEAR ITEMS</button>
                
                <?php if ($edit_mode): ?>
                    <a href="admin.php?page=stock_out" class="clear-btn" style="text-decoration: none; display: inline-block; padding: 10px 20px;">NEW DOCUMENT</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Compact Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content compact-modal">
        <span class="close">&times;</span>
        <h3>Edit Item</h3>
        <form id="editForm">
            <input type="hidden" id="edit-index">
            
            <!-- Compact Item Selection -->
            <div class="form-group">
                <label>Item:</label>
                <select id="edit-item-code" class="form-select item-dropdown compact-select" required>
                    <option value="">-- Select Item --</option>
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['item_Code'] ?>" 
                                data-name="<?= htmlspecialchars($i['item_Name']) ?>" 
                                data-price="<?= $i['price'] ?>">
                            <?= $i['item_Code'] ?> - <?= htmlspecialchars($i['item_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Compact Input Row -->
            <div class="compact-input-row">
                <div class="form-group compact-group">
                    <label>Qty:</label>
                    <input type="number" id="edit-quantity" min="1" step="1" class="form-input compact-input" required>
                </div>
                
                <div class="form-group compact-group">
                    <label>Price:</label>
                    <input type="number" id="edit-price" step="1" class="form-input compact-input" required>
                </div>
                
                <div class="form-group compact-group">
                    <label>Total:</label>
                    <input type="text" id="edit-total" readonly class="form-input compact-input total-display">
                </div>
            </div>
            
            <div class="modal-buttons compact-buttons">
                <button type="button" id="update-item-btn" class="save-btn compact-btn">Update</button>
                <button type="button" id="cancel-edit-btn" class="clear-btn compact-btn">Cancel</button>
            </div>
        </form>
    </div>
    </div>

<script>
// Show notification function
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Show existing PHP messages as notifications
<?php if (isset($success_message)): ?>
    showNotification('<?= addslashes($success_message) ?>', 'success');
<?php endif; ?>
<?php if (isset($error_message)): ?>
    showNotification('<?= addslashes($error_message) ?>', 'error');
<?php endif; ?>
<?php if (isset($info_message)): ?>
    showNotification('<?= addslashes($info_message) ?>', 'info');
<?php endif; ?>

// Document Edit/Search functionality
$(document).ready(function() {
    // Enable edit mode for document
    $('#enable_edit_btn').click(function() {
        $('#document_search_section').slideDown();
        $('#enable_edit_btn').hide();
        $('#search_doc_btn').show();
        $('#doc_no').prop('readonly', true);
        showNotification('Enter existing document number to edit', 'info');
    });
    
    // Cancel search
    $('#cancel_search_btn').click(function() {
        $('#document_search_section').slideUp();
        $('#enable_edit_btn').show();
        $('#search_doc_btn').hide();
        $('#doc_no').prop('readonly', false);
        $('#search_doc_no').val('');
    });
    
    // Validate document search
    $('#documentSearchForm').submit(function(e) {
        const docNo = $('#search_doc_no').val().trim();
        if (!docNo) {
            showNotification('Please enter a document number', 'error');
            e.preventDefault();
            return false;
        }
        return true;
    });
});

// Update customer address and phone automatically when customer is selected
$('#customer_select').change(function(){
    var selected = $(this).find('option:selected');
    var address = selected.data('address') || '';
    var phone = selected.data('phone') || '';
    
    $('#customer_address').val(address);
    $('#customer_phone').val(phone);
    // Sync hidden fields used by other forms
    var custId = $(this).val() || '';
    $('#hidden_customer').val(custId);
    $('#action_customer').val(custId);
});

// Initialize customer details on page load if customer is already selected
$(document).ready(function() {
    var selected = $('#customer_select').find('option:selected');
    var address = selected.data('address') || '';
    var phone = selected.data('phone') || '';
    
    $('#customer_address').val(address);
    $('#customer_phone').val(phone);
    // Initialize hidden fields with current selection
    var custId = $('#customer_select').val() || '';
    $('#hidden_customer').val(custId);
    $('#action_customer').val(custId);
});

// Helpers to keep totals in sync
function recalcTotal() {
    var qty = parseFloat($('#quantity').val() || '0');
    var price = parseFloat($('#price').val() || '0');
    var total = qty * price;
    if (!isNaN(total) && total > 0) {
        $('#total').val('₹' + total.toFixed(2));
    } else {
        $('#total').val('');
    }
}

function applyItemSelection() {
    var selected = $('#item_code').find('option:selected');
    var name = selected.data('name') || '';
    var price = selected.data('price');
    $('#item_name').val(name);
    if (price !== undefined && price !== null && price !== '') {
        $('#price').val(price);
    }
    recalcTotal();
}

// Update item details when item is selected
$('#item_code').on('change', applyItemSelection);

// Recalculate total when qty or price changes
$('#quantity').on('input', recalcTotal);
$('#price').on('input', recalcTotal);

// Initialize item-derived fields on page load
$(document).ready(function(){
    applyItemSelection();
    recalcTotal();
});

// Modal logic for compact edit (reusing same pattern as stock_in)
const modal = document.getElementById('editModal');
const closeBtn = document.querySelector('#editModal .close');
function enableEdit(index) {
    const row = document.getElementById('item-row-' + index);
    const qtyCell = row.querySelector('.item-qty');
    const priceCell = row.querySelector('.item-price');
    const totalCell = row.querySelector('.item-total');
    const currentQty = parseFloat(qtyCell.innerText);
    const currentPrice = parseFloat(priceCell.innerText.replace('₹','').replace(',',''));
    document.getElementById('edit-index').value = index;
    document.getElementById('edit-quantity').value = currentQty;
    document.getElementById('edit-price').value = currentPrice;
    document.getElementById('edit-total').value = '₹' + (currentQty * currentPrice).toFixed(2);
    $('#edit-item-code').val(row.querySelector('td:first-child').innerText.trim());
    modal.style.display = 'block';
}
closeBtn.onclick = () => { modal.style.display = 'none'; };
document.getElementById('cancel-edit-btn').onclick = () => { modal.style.display = 'none'; };
document.getElementById('edit-quantity').addEventListener('input', function(){
    const q = parseFloat(this.value || '0');
    const p = parseFloat(document.getElementById('edit-price').value || '0');
    document.getElementById('edit-total').value = '₹' + (q*p).toFixed(2);
});
document.getElementById('edit-price').addEventListener('input', function(){
    const p = parseFloat(this.value || '0');
    const q = parseFloat(document.getElementById('edit-quantity').value || '0');
    document.getElementById('edit-total').value = '₹' + (q*p).toFixed(2);
});
document.getElementById('update-item-btn').onclick = function(){
    const idx = document.getElementById('edit-index').value;
    const code = document.getElementById('edit-item-code').value;
    const qty = document.getElementById('edit-quantity').value;
    const price = document.getElementById('edit-price').value;
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="update_index" value="${idx}">
        <input type="hidden" name="new_item_code" value="${code}">
        <input type="hidden" name="new_quantity" value="${qty}">
        <input type="hidden" name="new_price" value="${price}">
        <input type="hidden" name="customer" value="<?= htmlspecialchars($selected_customer) ?>">
        <button type="submit" name="update_item" style="display:none;"></button>
    `;
    document.body.appendChild(form);
    form.querySelector('button[name=update_item]').click();
};
</script>
</body>
</html>
