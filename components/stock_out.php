<?php
// Add session start at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("db.php");

// Get logged-in user details from users table
$userId = null;
$userBranchId = null;
$userName = $_SESSION['userName'] ?? '';

if (!empty($userName)) {
    // Fetch user details from users table
    $stmt = $conn->prepare("SELECT userId, branchId FROM users WHERE userName = ?");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    if ($user_data) {
        $userId = $user_data['userId'];
        $userBranchId = $user_data['branchId'];
    } else {
        // Fallback if user not found
        $userId = $_SESSION['userId'] ?? 1;
        $userBranchId = $_SESSION['branchId'] ?? 1;
    }
} else {
    // Fallback if no username in session
    $userId = $_SESSION['userId'] ?? 1;
    $userBranchId = $_SESSION['branchId'] ?? 1;
}

// Validate user data
if (empty($userId) || empty($userBranchId)) {
    $_SESSION['error_message'] = "User data not found. Please login again.";
    header("Location: index.php");
    exit;
}

// Verify branch exists
$stmt = $conn->prepare("SELECT branchName FROM branch WHERE branchId = ?");
$stmt->bind_param("i", $userBranchId);
$stmt->execute();
$result = $stmt->get_result();
$branch_data = $result->fetch_assoc();
$branchName = $branch_data ? $branch_data['branchName'] : 'Unknown Branch';

// Initialize variables for edit mode
$edit_mode = false;
$editing_doc_no = '';
$existing_doc_data = null;
$existing_items = [];

// Handle NEW DOCUMENT action - clear all session data
if (isset($_GET['action']) && $_GET['action'] === 'new') {
    // Clear all stock-out related session data
    unset($_SESSION['so_added_items']);
    unset($_SESSION['so_form_data']);
    unset($_SESSION['so_edit_mode']);
    unset($_SESSION['so_editing_doc_no']);
    unset($_SESSION['so_customer_details']);
    unset($_SESSION['so_last_post']);
    
    // Initialize fresh session data
    $_SESSION['so_added_items'] = [];
    $_SESSION['so_form_data'] = [
        'customer' => '',
        'item_code' => '',
        'quantity' => '',
        'price' => '',
        'remarks' => ''
    ];
    $_SESSION['so_last_post'] = [];
    
    // Set success message
    $_SESSION['success_message'] = "New document created successfully!";
    
    // Redirect to clean URL
    header("Location: admin.php?page=stock_out");
    exit;
}

// Restore edit mode from session if available
if (isset($_SESSION['so_edit_mode']) && $_SESSION['so_edit_mode'] === true) {
    $edit_mode = true;
    $editing_doc_no = $_SESSION['so_editing_doc_no'] ?? '';
}

// Function to generate document number for stock out
function generateDocumentNumber($conn, $branchId, $userId) {
    $datePart = date('dmY'); // DDMMYYYY format
    $dateKey = date('Ymd'); // For sequence table key
    
    try {
        // Check if doc_sequence_out table exists, create if not
        $tableCheck = $conn->query("SHOW TABLES LIKE 'doc_sequence_out'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            // Create the table
            $createTable = "CREATE TABLE `doc_sequence_out` (
                `branchId` int(11) NOT NULL,
                `seq_date` char(8) NOT NULL,
                `next_no` int(11) NOT NULL,
                PRIMARY KEY (`branchId`, `seq_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
            $conn->query($createTable);
        }
        
        // Start transaction for atomic operation
        $conn->begin_transaction();
        
        // Lock sequence row for atomic increment
        $seqSelect = $conn->prepare("SELECT next_no FROM doc_sequence_out WHERE branchId = ? AND seq_date = ? FOR UPDATE");
        $seqSelect->bind_param("is", $branchId, $dateKey);
        $seqSelect->execute();
        $result = $seqSelect->get_result();
        $seqRow = $result->fetch_assoc();
        
        if ($seqRow) {
            $nextNo = intval($seqRow['next_no']);
            $upd = $conn->prepare("UPDATE doc_sequence_out SET next_no = next_no + 1 WHERE branchId = ? AND seq_date = ?");
            $upd->bind_param("is", $branchId, $dateKey);
            $upd->execute();
        } else {
            $nextNo = 1;
            $nextSeq = 2;
            $ins = $conn->prepare("INSERT INTO doc_sequence_out (branchId, seq_date, next_no) VALUES (?, ?, ?)");
            $ins->bind_param("isi", $branchId, $dateKey, $nextSeq);
            $ins->execute();
        }
        
        $conn->commit();
        
        // Format: SO + BRANCHID + USERID + DDMMYYYY + SEQUENCE (4 digits)
        $seqStr = str_pad((string)$nextNo, 4, '0', STR_PAD_LEFT);
        $docNo = "SO" . $branchId . $userId . $datePart . $seqStr;
        
        return $docNo;
    } catch (Exception $e) {
        if ($conn->connect_errno == 0) {
            $conn->rollback();
        }
        // Fallback to timestamp-based approach
        $timestamp = time();
        $fallbackDocNo = "SO" . $branchId . $userId . $datePart . substr($timestamp, -4);
        
        return $fallbackDocNo;
    }
}

// Handle document search for editing
if (isset($_POST['search_document'])) {
    $doc_no = trim($_POST['doc_no']);
    if (!empty($doc_no)) {
        // Check if document exists
        $stmt = $conn->prepare("SELECT * FROM trans_head WHERE Trans_Docs_No = ? AND flag = 1");
        $stmt->bind_param("s", $doc_no);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing_doc_data = $result->fetch_assoc();
        
        if ($existing_doc_data) {
            $edit_mode = true;
            $editing_doc_no = $doc_no;
            $_SESSION['so_edit_mode'] = true;
            $_SESSION['so_editing_doc_no'] = $doc_no;
            $_SESSION['so_editing_row_version'] = isset($existing_doc_data['row_version']) ? intval($existing_doc_data['row_version']) : 0;
            
            // Get existing items for this document
            $stmt = $conn->prepare("SELECT * FROM trans_line WHERE Trans_Docs_No = ? AND flag = 1");
            $stmt->bind_param("s", $doc_no);
            $stmt->execute();
            $result = $stmt->get_result();
            $existing_items = [];
            while ($row = $result->fetch_assoc()) {
                $existing_items[] = $row;
            }
            
            // Convert existing items to session format
            $_SESSION['so_added_items'] = [];
            foreach ($existing_items as $item) {
                // Get item name and use existing item_Id from trans_line
                $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
                $stmt->bind_param("s", $item['item_Code']);
                $stmt->execute();
                $result = $stmt->get_result();
                $item_data = $result->fetch_assoc();
                $item_name = $item_data ? $item_data['item_Name'] : '';
                
                $_SESSION['so_added_items'][] = [
                    'item_id' => $item['item_Id'], // Use item_Id from trans_line
                    'item_code' => $item['item_Code'],
                    'item_name' => $item_name,
                    'qty' => floatval($item['qty']),
                    'price' => floatval($item['rate']),
                    'total' => floatval($item['total'])
                ];
            }
            
            // Set form data from existing document
            $_SESSION['so_form_data'] = [
                'customer' => $existing_doc_data['custId'],
                'item_code' => '',
                'quantity' => '',
                'price' => '',
                'remarks' => $existing_doc_data['remarks']
            ];
            
            // Fetch and store customer details
            if (!empty($existing_doc_data['custId'])) {
                $stmt = $conn->prepare("SELECT custAdd, cust_phoneNo FROM customers WHERE custId = ?");
                $stmt->bind_param("i", $existing_doc_data['custId']);
                $stmt->execute();
                $result = $stmt->get_result();
                $customer_data = $result->fetch_assoc();
                if ($customer_data) {
                    $_SESSION['so_customer_details'] = [
                        'address' => $customer_data['custAdd'],
                        'phone' => $customer_data['cust_phoneNo']
                    ];
                }
            }
            
            $_SESSION['success_message'] = "Document loaded for editing!";
        } else {
            $_SESSION['error_message'] = "Document not found!";
        }
    }
}

// Handle document save/update
if (isset($_POST['save']) || isset($_POST['update_document'])) {
    $custId = $_POST['customer'] ?? '';
    $remarks = $_POST['remarks'] ?? "";
    
    // Update remarks in form data
    $_SESSION['so_form_data']['remarks'] = $remarks;
    
    if (!empty($_SESSION['so_added_items'])) {
        try {
            $conn->begin_transaction();
            
            if (isset($_POST['update_document'])) {
                // UPDATE EXISTING DOCUMENT
                $docNo = $_POST['doc_no'] ?? $editing_doc_no;
                
                // Load and lock the head row
                $lockStmt = $conn->prepare("SELECT row_version FROM trans_head WHERE Trans_Docs_No = ? FOR UPDATE");
                $lockStmt->bind_param("s", $docNo);
                $lockStmt->execute();
                $result = $lockStmt->get_result();
                $locked = $result->fetch_assoc();
                if (!$locked) {
                    throw new Exception("Document not found for update.");
                }
                
                $clientVersion = isset($_POST['row_version']) ? intval($_POST['row_version']) : ($_SESSION['so_editing_row_version'] ?? intval($locked['row_version']));
                
                // Update trans_head with optimistic locking
                $stmt = $conn->prepare("UPDATE trans_head SET custId = ?, remarks = ?, row_version = row_version + 1 WHERE Trans_Docs_No = ? AND row_version = ?");
                $stmt->bind_param("issi", $custId, $remarks, $docNo, $clientVersion);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    $conn->rollback();
                    $_SESSION['error_message'] = "Someone else modified this document. Please reload and try again.";
                    header("Location: admin.php?page=stock_out");
                    exit;
                }
                
                // Delete existing trans_line items
                $stmt = $conn->prepare("DELETE FROM trans_line WHERE Trans_Docs_No = ?");
                $stmt->bind_param("s", $docNo);
                $stmt->execute();
                
                // Insert updated trans_line items
                foreach ($_SESSION['so_added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO trans_line (Trans_Docs_No, item_Id, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, ?, 1)");
                    $stmt->bind_param("sissdd", $docNo, $item['item_id'], $item['item_code'], $item['qty'], $item['price'], $item['total']);
                    $stmt->execute();
                }
                
                $success_message = "Document updated successfully! Document No: $docNo";
                $_SESSION['so_editing_row_version'] = $clientVersion + 1;
            } else {
                // CREATE NEW DOCUMENT
                $docNo = generateDocumentNumber($conn, $userBranchId, $userId);

                // Insert into trans_head
                $stmt = $conn->prepare("INSERT INTO trans_head (Trans_Docs_No, Trans_date, custId, flag, remarks, userId, branchId) VALUES (?, NOW(), ?, 1, ?, ?, ?)");
                $stmt->bind_param("sisii", $docNo, $custId, $remarks, $userId, $userBranchId);
                $stmt->execute();

                // Insert each item into trans_line
                foreach ($_SESSION['so_added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO trans_line (Trans_Docs_No, item_Id, item_Code, qty, rate, total, flag) VALUES (?, ?, ?, ?, ?, ?, 1)");
                    $stmt->bind_param("sissdd", $docNo, $item['item_id'], $item['item_code'], $item['qty'], $item['price'], $item['total']);
                    $stmt->execute();
                }
                
                $success_message = "Stock Out saved successfully! Document No: $docNo";
            }
            
            $conn->commit();
            
            // Clear everything after successful save/update
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
            unset($_SESSION['so_customer_details']);
            
            $added_items = [];
            $form_data = $_SESSION['so_form_data'];
            $selected_customer = '';
            $customer_address = "";
            $customer_phone = "";
            $grand_total = 0;
            $edit_mode = false;
            $editing_doc_no = '';
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error " . ($edit_mode ? "updating" : "saving") . " document: " . $e->getMessage();
        }
    } else {
        $error_message = "Please add at least one item.";
    }
}

// Initialize session items if not set
if (!isset($_SESSION['so_added_items'])) {
    $_SESSION['so_added_items'] = [];
}

// Initialize form data in session if not set
if (!isset($_SESSION['so_form_data'])) {
    $_SESSION['so_form_data'] = [
        'customer' => '',
        'item_code' => '',
        'quantity' => '',
        'price' => '',
        'remarks' => ''
    ];
}

// Initialize reload protection
if (!isset($_SESSION['so_last_post'])) {
    $_SESSION['so_last_post'] = [];
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

$added_items = $_SESSION['so_added_items'];
$form_data = $_SESSION['so_form_data'];
$remarks = $form_data['remarks'];
$grand_total = 0;
$selected_customer = $form_data['customer'];
$customer_address = "";
$customer_phone = "";

// Use stored customer details from session if available (for edit mode)
if (isset($_SESSION['so_customer_details'])) {
    $customer_address = $_SESSION['so_customer_details']['address'];
    $customer_phone = $_SESSION['so_customer_details']['phone'];
} else if (!empty($selected_customer)) {
    // Fetch customer details if customer is selected and not in session
    $stmt = $conn->prepare("SELECT custAdd, cust_phoneNo FROM customers WHERE custId = ?");
    $stmt->bind_param("i", $selected_customer);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    if ($customer) {
        $customer_address = $customer['custAdd'];
        $customer_phone = $customer['cust_phoneNo'];
    }
}

// Calculate grand total
foreach ($added_items as $item) {
    $grand_total += $item['total'];
}

// Fetch customers for dropdown
$customers = [];
$result = $conn->query("SELECT custId, custName, custAdd, cust_phoneNo FROM customers ORDER BY custName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// Fetch items for dropdown
$items = [];
$result = $conn->query("SELECT item_Id, item_Code, item_Name, price FROM items ORDER BY item_Code ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add item - with duplicate prevention
    if (isset($_POST['add_item'])) {
        // Check if this is a duplicate submission
        $current_post = md5(serialize($_POST));
        if ($current_post !== ($_SESSION['so_last_post']['add_item'] ?? '')) {
            
            // Store this post to prevent duplicates
            $_SESSION['so_last_post']['add_item'] = $current_post;
            
            // Get form data
            $custId = $_POST['customer'] ?? '';
            $item_code = $_POST['item_code'];
            $qty = floatval($_POST['quantity']);
            $price = floatval($_POST['price']);
            $total = $qty * $price;

            // Store form data in session for persistence
            $_SESSION['so_form_data'] = [
                'customer' => $custId,
                'item_code' => $item_code,
                'quantity' => $_POST['quantity'],
                'price' => $_POST['price'],
                'remarks' => $form_data['remarks']
            ];
            $form_data = $_SESSION['so_form_data'];
            $selected_customer = $custId;

            // Get customer details and store in session
            if (!empty($custId)) {
                $stmt = $conn->prepare("SELECT custAdd, cust_phoneNo FROM customers WHERE custId = ?");
                $stmt->bind_param("i", $custId);
                $stmt->execute();
                $result = $stmt->get_result();
                $customer = $result->fetch_assoc();
                if ($customer) {
                    $customer_address = $customer['custAdd'];
                    $customer_phone = $customer['cust_phoneNo'];
                    
                    // Store customer details in session to persist across requests
                    $_SESSION['so_customer_details'] = [
                        'address' => $customer_address,
                        'phone' => $customer_phone
                    ];
                }
            }

            // Get item name and ID
            $stmt = $conn->prepare("SELECT item_Id, item_Name FROM items WHERE item_Code = ?");
            $stmt->bind_param("s", $item_code);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $item_name = $item ? $item['item_Name'] : '';
            $item_id = $item ? $item['item_Id'] : null;

            if ($item_name && $item_id) {
                $_SESSION['so_added_items'][] = [
                    'item_id' => $item_id,
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $total
                ];
                $added_items = $_SESSION['so_added_items'];
                $grand_total += $total;
                $success_message = "Item added successfully!";
                
                // Clear only the item-specific form fields, keep customer
                $_SESSION['so_form_data']['item_code'] = '';
                $_SESSION['so_form_data']['quantity'] = '';
                $_SESSION['so_form_data']['price'] = '';
                $form_data = $_SESSION['so_form_data'];
                
                // Refresh customer details from session after adding item
                if (isset($_SESSION['so_customer_details'])) {
                    $customer_address = $_SESSION['so_customer_details']['address'];
                    $customer_phone = $_SESSION['so_customer_details']['phone'];
                }
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
        if (isset($_SESSION['so_added_items'][$delete_index])) {
            $deleted_item = $_SESSION['so_added_items'][$delete_index];
            array_splice($_SESSION['so_added_items'], $delete_index, 1);
            $added_items = $_SESSION['so_added_items'];
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
        
        if (isset($_SESSION['so_added_items'][$update_index])) {
            // Get new item name and ID if item code changed
            if ($_SESSION['so_added_items'][$update_index]['item_code'] !== $new_item_code) {
                $stmt = $conn->prepare("SELECT item_Id, item_Name FROM items WHERE item_Code = ?");
                $stmt->bind_param("s", $new_item_code);
                $stmt->execute();
                $result = $stmt->get_result();
                $item = $result->fetch_assoc();
                $new_item_name = $item ? $item['item_Name'] : $_SESSION['so_added_items'][$update_index]['item_name'];
                $new_item_id = $item ? $item['item_Id'] : $_SESSION['so_added_items'][$update_index]['item_id'];
            } else {
                $new_item_name = $_SESSION['so_added_items'][$update_index]['item_name'];
                $new_item_id = $_SESSION['so_added_items'][$update_index]['item_id'];
            }
            
            $_SESSION['so_added_items'][$update_index]['item_id'] = $new_item_id;
            $_SESSION['so_added_items'][$update_index]['item_code'] = $new_item_code;
            $_SESSION['so_added_items'][$update_index]['item_name'] = $new_item_name;
            $_SESSION['so_added_items'][$update_index]['qty'] = $new_qty;
            $_SESSION['so_added_items'][$update_index]['price'] = $new_price;
            $_SESSION['so_added_items'][$update_index]['total'] = $new_qty * $new_price;
            $added_items = $_SESSION['so_added_items'];
            
            // Recalculate grand total
            $grand_total = 0;
            foreach ($added_items as $item) {
                $grand_total += $item['total'];
            }
            $success_message = "Item updated successfully!";
            
            // Redirect to refresh the page and show updated data
            $_SESSION['success_message'] = $success_message;
            header("Location: admin.php?page=stock_out");
            exit;
        }
    }

    // Clear items
    if (isset($_POST['clear'])) {
        $_SESSION['so_added_items'] = [];
        $_SESSION['so_last_post'] = [];
        $added_items = [];
        $grand_total = 0;
        $success_message = "All items cleared successfully!";
        
        // Keep form data but clear items
        $_SESSION['so_form_data']['item_code'] = '';
        $_SESSION['so_form_data']['quantity'] = '';
        $_SESSION['so_form_data']['price'] = '';
        $form_data = $_SESSION['so_form_data'];
    }
}

// Generate document number for display
if ($edit_mode) {
    $docNo = $editing_doc_no;
} else {
    // Preview document number (without actually generating sequence)
    $datePart = date('dmY'); // DDMMYYYY format
    $dateKey = date('Ymd'); // For sequence table key
    
    try {
        // Check if doc_sequence_out table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'doc_sequence_out'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            // Get next sequence number for preview (without locking)
            $seqSelect = $conn->prepare("SELECT next_no FROM doc_sequence_out WHERE branchId = ? AND seq_date = ?");
            $seqSelect->bind_param("is", $userBranchId, $dateKey);
            $seqSelect->execute();
            $result = $seqSelect->get_result();
            $seqRow = $result->fetch_assoc();
            $nextNo = $seqRow ? intval($seqRow['next_no']) : 1;
        } else {
            // Table doesn't exist, start with 1
            $nextNo = 1;
        }
        
        $seqStr = str_pad((string)$nextNo, 4, '0', STR_PAD_LEFT);
        $docNo = "SO" . $userBranchId . $userId . $datePart . $seqStr;
        
    } catch (Exception $e) {
        // Fallback to timestamp-based approach
        $timestamp = time();
        $docNo = "SO" . $userBranchId . $userId . $datePart . substr($timestamp, -4);
    }
}

// Get item name for display if item code is set
$current_item_name = '';
if (!empty($form_data['item_code'])) {
    $stmt = $conn->prepare("SELECT item_Name FROM items WHERE item_Code = ?");
    $stmt->bind_param("s", $form_data['item_code']);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $current_item_name = $item ? $item['item_Name'] : '';
}

// UI
?>
<title>Stock Out Entry</title>
<link rel="stylesheet" href="stock_in.css">
<link rel="stylesheet" href="crud.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body>
<!-- Notification Container -->
<div id="notification-container"></div>

<div class="main-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="first-line">
            <div class="document-info">
                <div class="doc-number-group">
                    <label>Document No:</label>
                    <div class="doc-input-container">
                        <input type="text" name="doc_no" id="doc_no" value="<?= $docNo ?>" 
                               class="doc-number-input" <?= $edit_mode ? 'readonly' : '' ?>>
                        <?php if (!$edit_mode): ?>
                            <button type="button" id="enable_edit_btn" class="edit-doc-btn">Edit</button>
                            <button type="button" id="search_doc_btn" class="search-doc-btn" style="display: none;">Edit Mode</button>
                        <?php else: ?>
                            <span class="edit-mode-badge">EDIT</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="doc-date-group">
                    <label>Date:</label>
                    <span class="date-value"><?= date('d/m/Y') ?></span>
                </div>
            </div>
            
            <!-- Customer Details -->
            <div class="supplier-details">
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
                                            <?= ($selected_customer == $c['custId']) ? 'selected' : '' ?>>
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

        <!-- Document Search Form -->
        <div id="document_search_section" style="display: none; margin-top: 8px; padding: 6px; background: #f5f5f5; border-radius: 3px;">
            <form method="post" id="documentSearchForm">
                <div style="display: flex; gap: 6px; align-items: center;">
                    <div style="flex: 1;">
                        <input type="text" name="doc_no" id="search_doc_no" placeholder="Enter Document No to edit" 
                               class="form-input" style="width: 100%; font-size: 10px; padding: 4px;">
                    </div>
                    <div>
                        <button type="submit" name="search_document" class="search-doc-btn">Load</button>
                    </div>
                    <div>
                        <button type="button" id="cancel_search_btn" class="clear-btn" style="font-size: 9px; padding: 4px 8px;">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Item Entry Section -->
    <div class="item-entry-section">
        <h2>Item Entry:</h2>
        <form method="post" class="item-form" id="itemForm">
            <input type="hidden" name="customer" id="hidden_customer" value="<?= $selected_customer ?>">
            
            <div class="item-form-row">
                <div class="form-group full-width">
                    <label>Select Item:</label>
                    <div class="searchable-dropdown" id="item-dropdown-container">
                        <input type="text" id="item-search" class="form-input item-search" 
                               placeholder="Search items by code or name..." autocomplete="off">
                        <input type="hidden" id="item_code" name="item_code" required>
                        <div class="dropdown-list" id="item-dropdown-list">
                            <div class="dropdown-item" data-value="" data-id="" data-name="" data-price="">
                               
                            </div>
                            <?php foreach ($items as $i): ?>
                                <div class="dropdown-item" 
                                     data-value="<?= $i['item_Code'] ?>"
                                     data-id="<?= $i['item_Id'] ?>"
                                     data-name="<?= htmlspecialchars($i['item_Name']) ?>" 
                                     data-price="<?= $i['price'] ?>"
                                     <?= ($form_data['item_code'] == $i['item_Code']) ? 'class="dropdown-item selected"' : '' ?>>
                                    <?= $i['item_Code'] ?> - <?= htmlspecialchars($i['item_Name'])  ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
                            <button type="button" class="edit-item-btn" onclick="enableEdit(<?= $index ?>)" data-index="<?= $index ?>" title="Edit Item">✎</button>
                            <form method="post" style="display: inline; margin-left: 2px;">
                                <input type="hidden" name="delete_index" value="<?= $index ?>">
                                <button type="submit" name="delete_item" class="delete-item-btn" onclick="return confirm('Delete this item?')" title="Delete Item">✕</button>
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
        
        <!-- Remarks Section -->
        <div class="remarks-section">
            <form method="post" class="action-form">
                <input type="hidden" name="customer" value="<?= $selected_customer ?>">
                <input type="hidden" name="doc_no" value="<?= $docNo ?>">
                
                <div class="form-group">
                    <label>Remarks:</label>
                    <textarea name="remarks" class="form-input" placeholder="Enter any remarks..." rows="2"><?= htmlspecialchars($remarks) ?></textarea>
                </div>
                
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="row_version" value="<?= isset($_SESSION['so_editing_row_version']) ? intval($_SESSION['so_editing_row_version']) : 0 ?>">
                    <button type="submit" name="update_document" class="save-btn">UPDATE DOCUMENT</button>
                <?php else: ?>
                    <button type="submit" name="save" class="save-btn">SAVE STOCK OUT</button>
                <?php endif; ?>
                
                <button type="submit" name="clear" class="clear-btn">CLEAR ITEMS</button>
                
                <?php if ($edit_mode): ?>
                    <a href="admin.php?page=stock_out&action=new" class="clear-btn" style="text-decoration: none; display: inline-block; padding: 10px 20px;">NEW DOCUMENT</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content compact-modal">
        <span class="close">&times;</span>
        <h3>Edit Item</h3>
        <form id="editForm">
            <input type="hidden" id="edit-index">
            
            <div class="form-group">
                <label>Item:</label>
                <div class="searchable-dropdown" id="edit-item-dropdown-container">
                    <input type="text" id="edit-item-search" class="form-input item-search compact-input" 
                           placeholder="Search items..." autocomplete="off">
                    <input type="hidden" id="edit-item-code" required>
                    <div class="dropdown-list" id="edit-item-dropdown-list">
                        <div class="dropdown-item" data-value="" data-id="" data-name="" data-price="">
                            -- Select Item --
                        </div>
                        <?php foreach ($items as $i): ?>
                            <div class="dropdown-item" 
                                 data-value="<?= $i['item_Code'] ?>"
                                 data-id="<?= $i['item_Id'] ?>"
                                 data-name="<?= htmlspecialchars($i['item_Name']) ?>" 
                                 data-price="<?= $i['price'] ?>">
                                <?= $i['item_Code'] ?> - <?= htmlspecialchars($i['item_Name']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
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
    
    // Update hidden customer field for item form
    $('#hidden_customer').val($(this).val());
});

// Initialize customer details on page load
$(document).ready(function() {
    var currentAddress = $('#customer_address').val();
    var currentPhone = $('#customer_phone').val();
    
    if (currentAddress === '' && currentPhone === '') {
        var selected = $('#customer_select').find('option:selected');
        if (selected.length > 0) {
            var address = selected.data('address') || '';
            var phone = selected.data('phone') || '';
            
            $('#customer_address').val(address);
            $('#customer_phone').val(phone);
        }
    }
});

// Searchable Dropdown Functionality
function initSearchableDropdown(searchInputId, dropdownListId, hiddenInputId, onSelectCallback) {
    const searchInput = document.getElementById(searchInputId);
    const dropdownList = document.getElementById(dropdownListId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const items = dropdownList.querySelectorAll('.dropdown-item');
    let highlightedIndex = -1;

    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        dropdownList.classList.add('show');
        filterItems('');
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.searchable-dropdown')) {
            dropdownList.classList.remove('show');
        }
    });

    // Filter items as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterItems(searchTerm);
        highlightedIndex = -1;
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = dropdownList.querySelectorAll('.dropdown-item:not([style*="display: none"])');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = Math.min(highlightedIndex + 1, visibleItems.length - 1);
            updateHighlight(visibleItems);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = Math.max(highlightedIndex - 1, 0);
            updateHighlight(visibleItems);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (highlightedIndex >= 0 && visibleItems[highlightedIndex]) {
                selectItem(visibleItems[highlightedIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdownList.classList.remove('show');
        }
    });

    // Handle item selection
    items.forEach(item => {
        item.addEventListener('click', function() {
            selectItem(this);
        });
    });

    function filterItems(searchTerm) {
        let hasVisibleItems = false;
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const itemCode = item.dataset.value.toLowerCase();
            const itemName = item.dataset.name.toLowerCase();
            
            if (text.includes(searchTerm) || itemCode.includes(searchTerm) || itemName.includes(searchTerm)) {
                item.style.display = 'block';
                hasVisibleItems = true;
                
                // Highlight search term
                if (searchTerm) {
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    item.innerHTML = item.textContent.replace(regex, '<span class="search-highlight">$1</span>');
                } else {
                    item.innerHTML = item.textContent;
                }
            } else {
                item.style.display = 'none';
            }
        });

        // Show "no results" message if needed
        let noResultsMsg = dropdownList.querySelector('.dropdown-no-results');
        if (!hasVisibleItems && searchTerm) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'dropdown-no-results';
                noResultsMsg.textContent = 'No items found';
                dropdownList.appendChild(noResultsMsg);
            }
            noResultsMsg.style.display = 'block';
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }

    function updateHighlight(visibleItems) {
        visibleItems.forEach((item, index) => {
            item.classList.toggle('highlighted', index === highlightedIndex);
        });
    }

    function selectItem(item) {
        const value = item.dataset.value;
        const name = item.dataset.name;
        const price = item.dataset.price;
        const id = item.dataset.id;
        
        // Update inputs
        searchInput.value = item.textContent.trim();
        hiddenInput.value = value;
        
        // Remove previous selection
        items.forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
        
        // Hide dropdown
        dropdownList.classList.remove('show');
        
        // Call callback function
        if (onSelectCallback) {
            onSelectCallback({
                value: value,
                name: name,
                price: price,
                id: id
            });
        }
    }
}

// Initialize main item dropdown
initSearchableDropdown('item-search', 'item-dropdown-list', 'item_code', function(selectedItem) {
    $('#item_name').val(selectedItem.name);
    $('#price').val(selectedItem.price);
    if ($('#quantity').val() === '') {
        $('#quantity').val('');
    }
    calculateTotal();
});

// Initialize edit modal dropdown
initSearchableDropdown('edit-item-search', 'edit-item-dropdown-list', 'edit-item-code', function(selectedItem) {
    $('#edit-price').val(selectedItem.price);
    calculateEditTotal();
});

// Function to reset searchable dropdowns
function resetSearchableDropdowns() {
    // Reset main dropdown
    document.getElementById('item-search').value = '';
    document.getElementById('item_code').value = '';
    document.querySelectorAll('#item-dropdown-list .dropdown-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Reset edit dropdown
    document.getElementById('edit-item-search').value = '';
    document.getElementById('edit-item-code').value = '';
    document.querySelectorAll('#edit-item-dropdown-list .dropdown-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Reset other form fields
    document.getElementById('item_name').value = '';
    document.getElementById('quantity').value = '';
    document.getElementById('price').value = '';
    document.getElementById('total').value = '';
}

// Initialize current selection if item is already selected
$(document).ready(function() {
    const currentItemCode = '<?= $form_data['item_code'] ?>';
    if (currentItemCode) {
        const mainDropdownItems = document.querySelectorAll('#item-dropdown-list .dropdown-item');
        mainDropdownItems.forEach(item => {
            if (item.dataset.value === currentItemCode) {
                document.getElementById('item-search').value = item.textContent.trim();
                item.classList.add('selected');
            }
        });
    }
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

closeBtn.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Calculate total in edit modal
document.getElementById('edit-quantity').addEventListener('input', calculateEditTotal);
document.getElementById('edit-price').addEventListener('input', calculateEditTotal);

function calculateEditTotal() {
    var qty = parseFloat(document.getElementById('edit-quantity').value) || 0;
    var price = parseFloat(document.getElementById('edit-price').value) || 0;
    document.getElementById('edit-total').value = '₹' + (qty * price).toFixed(2);
}

// Enable edit mode for an item
function enableEdit(index) {
    const row = document.getElementById('item-row-' + index);
    const itemCode = row.querySelector('td:nth-child(1)').textContent.trim();
    const itemName = row.querySelector('td:nth-child(2)').textContent.trim();
    const qty = parseFloat(row.querySelector('.item-qty').textContent);
    const price = parseFloat(row.querySelector('.item-price').textContent.replace('₹', ''));
    
    document.getElementById('edit-index').value = index;
    
    // Set the searchable dropdown values
    document.getElementById('edit-item-search').value = itemCode + ' - ' + itemName;
    document.getElementById('edit-item-code').value = itemCode;
    
    // Mark the correct item as selected in the dropdown
    const editDropdownItems = document.querySelectorAll('#edit-item-dropdown-list .dropdown-item');
    editDropdownItems.forEach(item => {
        item.classList.remove('selected');
        if (item.dataset.value === itemCode) {
            item.classList.add('selected');
        }
    });
    
    document.getElementById('edit-quantity').value = qty;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-total').value = '₹' + (qty * price).toFixed(2);
    
    modal.style.display = 'block';
}

// Update item
document.getElementById('update-item-btn').onclick = function() {
    const index = document.getElementById('edit-index').value;
    const newItemCode = document.getElementById('edit-item-code').value;
    const newQty = parseFloat(document.getElementById('edit-quantity').value);
    const newPrice = parseFloat(document.getElementById('edit-price').value);
    
    // Show notification that update is being processed
    showNotification('Updating item...', 'info');
    
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
    
    const updateInput = document.createElement('input');
    updateInput.type = 'hidden';
    updateInput.name = 'update_item';
    updateInput.value = '1';
    
    form.appendChild(indexInput);
    form.appendChild(itemCodeInput);
    form.appendChild(qtyInput);
    form.appendChild(priceInput);
    form.appendChild(updateInput);
    
    document.body.appendChild(form);
    
    // Close modal before submitting
    modal.style.display = 'none';
    
    form.submit();
}

// Cancel edit
document.getElementById('cancel-edit-btn').onclick = function() {
    modal.style.display = 'none';
    showNotification('Edit cancelled', 'info');
}

// Form validation
$(document).ready(function() {
    // Handle clear items with confirmation
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
        const customer = $('#customer_select').val();
        
        if (itemCount === 0) {
            showNotification('Please add at least one item before saving.', 'error');
            e.preventDefault();
            return false;
        }
        
        if (!customer) {
            showNotification('Please select a customer before saving.', 'error');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

</body>
</html>
