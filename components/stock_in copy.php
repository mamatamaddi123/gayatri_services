<?php
// Add session start at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
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

// Initialize variables for edit mode
$edit_mode = false;
$editing_doc_no = '';
$existing_doc_data = null;
$existing_items = [];

// Handle document search for editing
if (isset($_POST['search_document'])) {
    $doc_no = trim($_POST['doc_no']);
    if (!empty($doc_no)) {
        // Check if document exists
        $stmt = $conn->prepare("SELECT * FROM Trans_Head WHERE Trans_Docs_No = ? AND flag = 0");
        $stmt->execute([$doc_no]);
        $existing_doc_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_doc_data) {
            $edit_mode = true;
            $editing_doc_no = $doc_no;
            
            // Get existing items for this document
            $stmt = $conn->prepare("SELECT * FROM Trans_Line WHERE Trans_Docs_No = ? AND flag = 0");
            $stmt->execute([$doc_no]);
            $existing_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert existing items to session format
            $_SESSION['added_items'] = [];
            foreach ($existing_items as $item) {
                // Get item name
                $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
                $stmt->execute([$item['item_Code']]);
                $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $item_name = $item_data ? $item_data['item_Name'] : '';
                
                $_SESSION['added_items'][] = [
                    'item_code' => $item['item_Code'],
                    'item_name' => $item_name,
                    'qty' => floatval($item['qty']),
                    'price' => floatval($item['rate']),
                    'total' => floatval($item['total'])
                ];
            }
            
            // Set form data from existing document
            $_SESSION['form_data'] = [
                'supplier' => $existing_doc_data['custId'],
                'item_code' => '',
                'quantity' => '',
                'price' => '',
                'remarks' => $existing_doc_data['remarks']
            ];
            
            $_SESSION['success_message'] = "Document loaded for editing!";
        } else {
            $_SESSION['error_message'] = "Document not found!";
        }
    }
}

// Handle document update - FIXED: This should handle both save and update scenarios
if (isset($_POST['save']) || isset($_POST['update_document'])) {
    $supId = $_POST['supplier'] ?? $selected_supplier;
    $remarks = $_POST['remarks'] ?? "";
    
    // Update remarks in form data
    $_SESSION['form_data']['remarks'] = $remarks;
    $form_data['remarks'] = $remarks;
    
    if (!empty($_SESSION['added_items'])) {
        try {
            $conn->beginTransaction();
            
            if ($edit_mode) {
                // UPDATE EXISTING DOCUMENT
                $docNo = $editing_doc_no;
                
                // Update Trans_Head
                $stmt = $conn->prepare("UPDATE Trans_Head SET custId = ?, remarks = ? WHERE Trans_Docs_No = ?");
                $stmt->execute([$supId, $remarks, $docNo]);
                
                // Delete existing Trans_Line items
                $stmt = $conn->prepare("DELETE FROM Trans_Line WHERE Trans_Docs_No = ?");
                $stmt->execute([$docNo]);
                
                // Insert updated Trans_Line items
                foreach ($_SESSION['added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO Trans_Line (Trans_Docs_No, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$docNo, $item['item_code'], $item['qty'], $item['price'], $item['total']]);
                }
                
                $success_message = "Document updated successfully! Document No: $docNo";
            } else {
                // CREATE NEW DOCUMENT
                $userId = $_SESSION['userId'] ?? 1;
                $branchId = 1;
                
                // Get branch details for prefix
                $stmt = $conn->prepare("SELECT branchName FROM branch WHERE branchId = ?");
                $stmt->execute([$branchId]);
                $branch = $stmt->fetch(PDO::FETCH_ASSOC);
                $branchPrefix = "SI"; // Default prefix
                
                if ($branch && isset($branch['branchName'])) {
                    $branchPrefix = strtoupper(substr($branch['branchName'], 0, 2));
                }
                
                $timestamp = time();
                $datePart = date('Ymd');
                $docNo = $branchPrefix . "-SI-" . $datePart . "-" . $timestamp;

                // Insert into Trans_Head
                $stmt = $conn->prepare("INSERT INTO Trans_Head (Trans_Docs_No, Trans_date, custId, flag, remarks, userId, branchId) VALUES (?, NOW(), ?, 0, ?, ?, ?)");
                $stmt->execute([$docNo, $supId, $remarks, $userId, $branchId]);

                // Insert each item into Trans_Line
                foreach ($_SESSION['added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO Trans_Line (Trans_Docs_No, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$docNo, $item['item_code'], $item['qty'], $item['price'], $item['total']]);
                }
                
                $success_message = "Stock In saved successfully! Document No: $docNo";
            }
            
            $conn->commit();
            
            // Clear everything after successful save/update
            $_SESSION['added_items'] = [];
            $_SESSION['form_data'] = [
                'supplier' => '',
                'item_code' => '',
                'quantity' => '',
                'price' => '',
                'remarks' => ''
            ];
            $_SESSION['last_post'] = [];
            $added_items = [];
            $form_data = $_SESSION['form_data'];
            $selected_supplier = '';
            $supplier_address = "";
            $supplier_phone = "";
            $grand_total = 0;
            $edit_mode = false;
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error_message = "Error " . ($edit_mode ? "updating" : "saving") . " document: " . $e->getMessage();
        }
    } else {
        $error_message = "Please add at least one item.";
    }
}

// Initialize session items if not set
if (!isset($_SESSION['added_items'])) {
    $_SESSION['added_items'] = [];
}

// Initialize form data in session if not set
if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = [
        'supplier' => '',
        'item_code' => '',
        'quantity' => '',
        'price' => '',
        'remarks' => ''
    ];
}

// Initialize reload protection
if (!isset($_SESSION['last_post'])) {
    $_SESSION['last_post'] = [];
}

// Display messages from session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['info_message'])) {
    $info_message = $_SESSION['info_message'];
    unset($_SESSION['info_message']);
}

$added_items = $_SESSION['added_items'];
$form_data = $_SESSION['form_data'];
$remarks = $form_data['remarks'];
$grand_total = 0;
$selected_supplier = $form_data['supplier'];
$supplier_address = "";
$supplier_phone = "";

// Calculate grand total
foreach ($added_items as $item) {
    $grand_total += $item['total'];
}

// Fetch suppliers for dropdown
$suppliers = $conn->query("SELECT supId, supName, supAdd, phoneNo FROM suppliers ORDER BY supName ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch items for dropdown
$items = $conn->query("SELECT item_Code, item_Name, price FROM items ORDER BY item_Code ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get supplier details if supplier is selected
if (!empty($selected_supplier)) {
    $stmt = $conn->prepare("SELECT supAdd, phoneNo FROM suppliers WHERE supId = ?");
    $stmt->execute([$selected_supplier]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($supplier) {
        $supplier_address = $supplier['supAdd'];
        $supplier_phone = $supplier['phoneNo'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add item - with duplicate prevention
    if (isset($_POST['add_item'])) {
        // Check if this is a duplicate submission
        $current_post = md5(serialize($_POST));
        if ($current_post !== ($_SESSION['last_post']['add_item'] ?? '')) {
            
            // Store this post to prevent duplicates
            $_SESSION['last_post']['add_item'] = $current_post;
            
            // Get form data
            $supId = $_POST['supplier'] ?? '';
            $item_code = $_POST['item_code'];
            $qty = floatval($_POST['quantity']);
            $price = floatval($_POST['price']);
            $total = $qty * $price;

            // Store form data in session for persistence
            $_SESSION['form_data'] = [
                'supplier' => $supId,
                'item_code' => $item_code,
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price'],
                'remarks' => $form_data['remarks']
            ];
            $form_data = $_SESSION['form_data'];
            $selected_supplier = $supId;

            // Get supplier details
            if (!empty($supId)) {
                $stmt = $conn->prepare("SELECT supAdd, phoneNo FROM suppliers WHERE supId = ?");
                $stmt->execute([$supId]);
                $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($supplier) {
                    $supplier_address = $supplier['supAdd'];
                    $supplier_phone = $supplier['phoneNo'];
                }
            }

            // Get item name
            $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
            $stmt->execute([$item_code]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $item_name = $item ? $item['item_Name'] : '';

            if ($item_name) {
                $_SESSION['added_items'][] = [
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $total
                ];
                $added_items = $_SESSION['added_items'];
                $grand_total += $total;
                $success_message = "Item added successfully!";
                
                // Clear only the item-specific form fields, keep supplier
                $_SESSION['form_data']['item_code'] = '';
                $_SESSION['form_data']['quantity'] = '';
                $_SESSION['form_data']['price'] = '';
                $form_data = $_SESSION['form_data'];
            } else {
                $error_message = "Item not found!";
            }
        } else {
            // This is a duplicate submission - ignore it
            $info_message = "Item was already added. Continuing with current items.";
        }
    }

    // Delete item
    if (isset($_POST['delete_item'])) {
        $delete_index = $_POST['delete_index'];
        if (isset($_SESSION['added_items'][$delete_index])) {
            $deleted_item = $_SESSION['added_items'][$delete_index];
            array_splice($_SESSION['added_items'], $delete_index, 1);
            $added_items = $_SESSION['added_items'];
            $success_message = "Item '{$deleted_item['item_name']}' deleted successfully!";
            
            // Recalculate grand total
            $grand_total = 0;
            foreach ($added_items as $item) {
                $grand_total += $item['total'];
            }
        }
    }

    // Update item
    if (isset($_POST['update_item'])) {
        $update_index = $_POST['update_index'];
        $new_item_code = $_POST['new_item_code'];
        $new_qty = floatval($_POST['new_quantity']);
        $new_price = floatval($_POST['new_price']);
        
        if (isset($_SESSION['added_items'][$update_index])) {
            // Get new item name if item code changed
            if ($_SESSION['added_items'][$update_index]['item_code'] !== $new_item_code) {
                $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
                $stmt->execute([$new_item_code]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                $new_item_name = $item ? $item['item_Name'] : $_SESSION['added_items'][$update_index]['item_name'];
            } else {
                $new_item_name = $_SESSION['added_items'][$update_index]['item_name'];
            }
            
            $_SESSION['added_items'][$update_index]['item_code'] = $new_item_code;
            $_SESSION['added_items'][$update_index]['item_name'] = $new_item_name;
            $_SESSION['added_items'][$update_index]['qty'] = $new_qty;
            $_SESSION['added_items'][$update_index]['price'] = $new_price;
            $_SESSION['added_items'][$update_index]['total'] = $new_qty * $new_price;
            $added_items = $_SESSION['added_items'];
            
            // Recalculate grand total
            $grand_total = 0;
            foreach ($added_items as $item) {
                $grand_total += $item['total'];
            }
            $success_message = "Item updated successfully!";
        }
    }

    // Clear items
    if (isset($_POST['clear'])) {
        $_SESSION['added_items'] = [];
        $_SESSION['last_post'] = []; // Clear post history on clear
        $added_items = [];
        $grand_total = 0;
        $success_message = "All items cleared successfully!";
        
        // Keep form data but clear items
        $_SESSION['form_data']['item_code'] = '';
        $_SESSION['form_data']['quantity'] = '';
        $_SESSION['form_data']['price'] = '';
        $form_data = $_SESSION['form_data'];
    }
}

// Generate document number for display
if ($edit_mode) {
    $docNo = $editing_doc_no;
} else {
    $userId = $_SESSION['userId'] ?? 1;
    $branchId = 1;

    // Get branch details for prefix
    $stmt = $conn->prepare("SELECT branchName FROM branch WHERE branchId = ?");
    $stmt->execute([$branchId]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);
    $branchPrefix = "SI"; // Default prefix

    if ($branch && isset($branch['branchName'])) {
        $branchPrefix = strtoupper(substr($branch['branchName'], 0, 2));
    }

    $timestamp = time();
    $datePart = date('Ymd');
    $docNo = $branchPrefix . "-SI-" . $datePart . "-" . $timestamp;
}

// Get item name for display if item code is set
$current_item_name = '';
if (!empty($form_data['item_code'])) {
    $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
    $stmt->execute([$form_data['item_code']]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_item_name = $item ? $item['item_Name'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock In Entry</title>
<link rel="stylesheet" href="stock_in.css">
<link rel="stylesheet" href="crud.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Notification Container -->
<div id="notification-container"></div>

<div class="main-container">
    <!-- Combined Document & Supplier Header -->
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
                    Enter existing document number to edit previous stock in entry
                </p>
            </form>
        </div>

        <!-- Second Line: Supplier Details -->
        <div class="second-line">
            <div class="supplier-details">
                <h3>Supplier Details:</h3>
                <form method="post" class="supplier-form" id="supplierForm">
                    <div class="supplier-fields">
                        <div class="form-group">
                            <label>Select Supplier:</label>
                            <select name="supplier" id="supplier_select" required class="form-select">
                                <option value="">-- Select Supplier --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['supId'] ?>" 
                                            data-address="<?= htmlspecialchars($s['supAdd']) ?>"
                                            data-phone="<?= htmlspecialchars($s['phoneNo']) ?>"
                                            <?= ($selected_supplier == $s['supId']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['supName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <input type="text" id="supplier_address" value="<?= htmlspecialchars($supplier_address) ?>" readonly class="form-input address-field">
                        </div>
                        <div class="form-group">
                            <label>Phone:</label>
                            <input type="text" id="supplier_phone" value="<?= htmlspecialchars($supplier_phone) ?>" readonly class="form-input phone-field">
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
            <!-- Hidden field to preserve supplier selection -->
            <input type="hidden" name="supplier" id="hidden_supplier" value="<?= $selected_supplier ?>">
            
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
                    <input type="number" name="price" id="price" step="0.01" class="form-input" placeholder="Price" required 
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
                <input type="hidden" name="supplier" value="<?= $selected_supplier ?>">
                <input type="hidden" name="remarks" value="<?= htmlspecialchars($remarks) ?>">
                <input type="hidden" name="doc_no" value="<?= $docNo ?>">
                
                <?php if ($edit_mode): ?>
                    <button type="submit" name="update_document" class="save-btn">UPDATE DOCUMENT</button>
                <?php else: ?>
                    <button type="submit" name="save" class="save-btn">SAVE STOCK IN</button>
                <?php endif; ?>
                
                <button type="submit" name="clear" class="clear-btn">CLEAR ITEMS</button>
                
                <?php if ($edit_mode): ?>
                    <a href="admin.php?page=stock_in" class="clear-btn" style="text-decoration: none; display: inline-block; padding: 10px 20px;">NEW DOCUMENT</a>
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
                    <input type="number" id="edit-quantity" min="1" step="0.01" class="form-input compact-input" required>
                </div>
                
                <div class="form-group compact-group">
                    <label>Price:</label>
                    <input type="number" id="edit-price" step="0.01" class="form-input compact-input" required>
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

// Update supplier address and phone automatically when supplier is selected
$('#supplier_select').change(function(){
    var selected = $(this).find('option:selected');
    var address = selected.data('address') || '';
    var phone = selected.data('phone') || '';
    
    $('#supplier_address').val(address);
    $('#supplier_phone').val(phone);
});

// Initialize supplier details on page load if supplier is already selected
$(document).ready(function() {
    var selected = $('#supplier_select').find('option:selected');
    var address = selected.data('address') || '';
    var phone = selected.data('phone') || '';
    
    $('#supplier_address').val(address);
    $('#supplier_phone').val(phone);
});

// Update item details when item is selected
$('#item_code').change(function(){
    var selected = $(this).find('option:selected');
    $('#item_name').val(selected.data('name'));
    $('#price').val(selected.data('price'));
    // Don't clear quantity if it already has a value
    if ($('#quantity').val() === '') {
        $('#quantity').val('');
    }
    calculateTotal();
});

// Calculate total automatically
$('#quantity, #price').on('input', function(){
    calculateTotal();
});

function calculateTotal() {
    var qty = parseFloat($('#quantity').val()) || 0;
    var price = parseFloat($('#price').val()) || 0;
    $('#total').val('₹' + (qty * price).toFixed(2));
}

// Edit Modal functionality
const modal = document.getElementById('editModal');
const closeBtn = document.querySelector('.close');

// Close modal when X is clicked
closeBtn.onclick = function() {
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Update item details when item is selected in edit modal
$('#edit-item-code').change(function(){
    var selected = $(this).find('option:selected');
    $('#edit-price').val(selected.data('price'));
    calculateEditTotal();
});

// Calculate total in edit modal
document.getElementById('edit-quantity').addEventListener('input', calculateEditTotal);
document.getElementById('edit-price').addEventListener('input', calculateEditTotal);

function calculateEditTotal() {
    var qty = parseFloat(document.getElementById('edit-quantity').value) || 0;
    var price = parseFloat(document.getElementById('edit-price').value) || 0;
    document.getElementById('edit-total').value = '₹' + (qty * price).toFixed(2);
}

// Enable edit mode for an item with item dropdown
function enableEdit(index) {
    const row = document.getElementById('item-row-' + index);
    const itemCode = row.querySelector('td:nth-child(1)').textContent.trim();
    const qty = parseFloat(row.querySelector('.item-qty').textContent);
    const price = parseFloat(row.querySelector('.item-price').textContent.replace('₹', ''));
    
    // Populate modal with current values
    document.getElementById('edit-index').value = index;
    
    // Set the item dropdown
    $('#edit-item-code').val(itemCode);
    
    document.getElementById('edit-quantity').value = qty;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-total').value = '₹' + (qty * price).toFixed(2);
    
    // Show modal
    modal.style.display = 'block';
}

// Update item with enhanced functionality
document.getElementById('update-item-btn').onclick = function() {
    const index = document.getElementById('edit-index').value;
    const newItemCode = document.getElementById('edit-item-code').value;
    const newQty = parseFloat(document.getElementById('edit-quantity').value);
    const newPrice = parseFloat(document.getElementById('edit-price').value);
    
    if (!newItemCode) {
        showNotification('Please select an item.', 'error');
        return;
    }
    
    if (newQty <= 0) {
        showNotification('Please enter a valid quantity (greater than 0).', 'error');
        return;
    }
    
    if (newPrice < 0) {
        showNotification('Please enter a valid price (0 or greater).', 'error');
        return;
    }
    
    // Submit the form to update the item
    const form = document.createElement('form');
    form.method = 'post';
    form.style.display = 'none';
    
    const indexInput = document.createElement('input');
    indexInput.type = 'hidden';
    indexInput.name = 'update_index';
    indexInput.value = index;
    
    const itemCodeInput = document.createElement('input');
    itemCodeInput.type = 'hidden';
    itemCodeInput.name = 'new_item_code';
    itemCodeInput.value = newItemCode;
    
    const qtyInput = document.createElement('input');
    qtyInput.type = 'hidden';
    qtyInput.name = 'new_quantity';
    qtyInput.value = newQty;
    
    const priceInput = document.createElement('input');
    priceInput.type = 'hidden';
    priceInput.name = 'new_price';
    priceInput.value = newPrice;
    
    form.appendChild(indexInput);
    form.appendChild(itemCodeInput);
    form.appendChild(qtyInput);
    form.appendChild(priceInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Cancel edit
document.getElementById('cancel-edit-btn').onclick = function() {
    modal.style.display = 'none';
    showNotification('Edit cancelled', 'info');
}

// Enhanced form submission handlers
$(document).ready(function() {
    // Handle clear items with enhanced confirmation
    $('button[name="clear"]').click(function(e) {
        const itemCount = document.querySelectorAll('#items-tbody tr').length;
        if (itemCount > 0) {
            if (!confirm(`Are you sure you want to clear all ${itemCount} items? This action cannot be undone.`)) {
                e.preventDefault();
            }
        } else {
            showNotification('No items to clear.', 'info');
            e.preventDefault();
        }
    });
    
    // Handle save with validation
    $('button[name="save"], button[name="update_document"]').click(function(e) {
        const itemCount = document.querySelectorAll('#items-tbody tr').length;
        const supplier = $('#supplier_select').val();
        
        if (itemCount === 0) {
            showNotification('Please add at least one item before saving.', 'error');
            e.preventDefault();
            return false;
        }
        
        if (!supplier) {
            showNotification('Please select a supplier before saving.', 'error');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

</body>
</html>