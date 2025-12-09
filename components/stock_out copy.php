<?php
// Start session
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

// Initialize variables
$customer_id = "";
$item_code = "";
$qty = "";
$price = "";
$total = "";
$added_items = [];
$remarks = "";
$grand_total = 0;

// Initialize session items if not set
if (!isset($_SESSION['added_items'])) {
    $_SESSION['added_items'] = [];
}

// Get added items from session
$added_items = $_SESSION['added_items'];

// Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add item to list
    if (isset($_POST['add_item'])) {
        $customer_id = $_POST['customer'];
        $item_code = $_POST['item_code'];
        $qty = $_POST['quantity'];
        $price = $_POST['price'];

        // Get item details for name
        if (!empty($item_code)) {
            $stmt = $conn->prepare("SELECT item_Code, item_Name, price FROM items WHERE item_Code = ?");
            $stmt->execute([$item_code]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item) {
                $item_name = $item['item_Name'];
                $total = $price * $qty;
                
                $_SESSION['added_items'][] = [
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $total
                ];
                
                $added_items = $_SESSION['added_items'];
                
                $item_code = "";
                $qty = "";
                $price = "";
            } else {
                echo "<script>alert('Item not found!');</script>";
            }
        }
    }
    
    // Save stock out
    if (isset($_POST['save'])) {
        if (!empty($_SESSION['added_items'])) {
            $customer_id = $_POST['customer'];
            $remarks = $_POST['remarks'];
            
            $grand_total = 0;
            foreach ($_SESSION['added_items'] as $item) {
                $grand_total += $item['total'];
            }
            
            $stmt = $conn->prepare("SHOW TABLES LIKE 'stock_outs'");
            $stmt->execute();
            $stock_outs_exists = $stmt->fetch();
            
            if ($stock_outs_exists) {
                $stmt = $conn->prepare("SHOW COLUMNS FROM stock_outs");
                $stmt->execute();
                $stock_out_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (in_array('custId', $stock_out_columns)) {
                    $stmt = $conn->prepare("INSERT INTO stock_outs (custId, remarks, total_amount, stock_out_date) VALUES (?, ?, ?, NOW())");
                } else if (in_array('customer_id', $stock_out_columns)) {
                    $stmt = $conn->prepare("INSERT INTO stock_outs (customer_id, remarks, total_amount, stock_out_date) VALUES (?, ?, ?, NOW())");
                } else {
                    $stmt = $conn->prepare("INSERT INTO stock_outs (custId, remarks, total_amount, created_at) VALUES (?, ?, ?, NOW())");
                }
                $stmt->execute([$customer_id, $remarks, $grand_total]);
                $stock_out_id = $conn->lastInsertId();
                
                foreach ($_SESSION['added_items'] as $item) {
                    $stmt = $conn->prepare("INSERT INTO stock_out_items (stock_out_id, item_code, qty, price, total) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$stock_out_id, $item['item_code'], $item['quantity'], $item['price'], $item['total']]);
                }
                
                echo "<script>alert('Stock out recorded successfully!');</script>";
            } else {
                echo "<script>alert('Stock out recorded! (Note: stock_outs table not found)');</script>";
            }
            
            unset($_SESSION['added_items']);
            $added_items = [];
            $customer_id = "";
            $remarks = "";
        } else {
            echo "<script>alert('Please add items before saving!');</script>";
        }
    }
    
    // Clear items
    if (isset($_POST['clear'])) {
        unset($_SESSION['added_items']);
        $added_items = [];
        echo "<script>alert('All items cleared!');</script>";
    }
}

// Get customers and items
$customers = $conn->query("SELECT custId, custName, custAdd, cust_phoneNo FROM customers ORDER BY custName")->fetchAll(PDO::FETCH_ASSOC);
$items = $conn->query("SELECT item_Code, item_Name, price FROM items ORDER BY item_Name")->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$grand_total = 0;
foreach ($added_items as $item) {
    $grand_total += $item['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Out Management</title>
    <link rel="stylesheet" href="stock_out.css">
</head>
<body>
    
        
        
        <form method="POST" action="">
            <div class="form-section">
                <!-- <h2>Customer Information</h2> -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer">Select Customer:</label>
                        <select id="customer" name="customer" required>
                            <option value="">-- Select Customer --</option>
                            <?php foreach ($customers as $customer): ?>
                                <option 
                                    value="<?php echo $customer['custId']; ?>" 
                                    data-address="<?php echo htmlspecialchars($customer['custAdd']); ?>" 
                                    data-phone="<?php echo htmlspecialchars($customer['cust_phoneNo']); ?>"
                                    <?php echo ($customer_id == $customer['custId']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($customer['custName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cust_address">Address:</label>
                        <input type="text" id="cust_address" name="cust_address" readonly>
                    </div>

                    <div class="form-group">
                        <label for="cust_phone">Phone Number:</label>
                        <input type="text" id="cust_phone" name="cust_phone" readonly>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
    <h2>Add Item</h2>
    <div class="item-inline-row">
        <div class="form-group">
            <label for="item_code">Item Code:</label>
            <select id="item_code" name="item_code" required>
                <option value="">-- Select Item --</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['item_Code']; ?>" 
                            data-price="<?php echo $item['price']; ?>" 
                            data-name="<?php echo htmlspecialchars($item['item_Name']); ?>">
                        <?php echo htmlspecialchars($item['item_Code'] . ' - ' . $item['item_Name'] . ' (₹' . number_format($item['price'], 2) . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?php echo htmlspecialchars($qty); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="total">Total:</label>
            <input type="text" id="total" name="total" value="<?php echo htmlspecialchars($total); ?>" readonly>
        </div>
    </div>
    
    <button type="submit" name="add_item" class="btn btn-primary">ADD ITEM</button>
</div>

        </form>
        
        <?php if (!empty($added_items)): ?>
        <div class="form-section">
            <h2>LIST OF ITEMS ADDED</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($added_items as $index => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_code']); ?></td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>₹<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-section">
                <h3>TOTAL: ₹<?php echo number_format($grand_total, 2); ?></h3>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">
                <div class="form-group">
                    <label for="remarks">Remarks:</label>
                    <textarea id="remarks" name="remarks" rows="3" placeholder="Enter any remarks here..."><?php echo htmlspecialchars($remarks); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="save" class="btn btn-success">SAVE STOCK OUT</button>
                    <button type="submit" name="clear" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all items?')">CLEAR ALL ITEMS</button>
                    <a href="view_stock_outs.php" class="btn btn-info">VIEW SAVED STOCK OUTS</a>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="form-section">
            <p class="no-items">No items added yet. Add items using the form above.</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-fill address & phone for customer
        document.getElementById('customer').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('cust_address').value = selectedOption.getAttribute('data-address') || "";
            document.getElementById('cust_phone').value = selectedOption.getAttribute('data-phone') || "";
        });

        // Update price automatically when item selected
        document.getElementById('item_code').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value !== "") {
                document.getElementById('price').value = selectedOption.getAttribute('data-price');
                calculateTotal();
            }
        });
        
        // Calculate total
        document.getElementById('quantity').addEventListener('input', calculateTotal);
        document.getElementById('price').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            var quantity = document.getElementById('quantity').value;
            var price = document.getElementById('price').value;
            
            if (quantity && price) {
                var total = parseFloat(quantity) * parseFloat(price);
                document.getElementById('total').value = '₹' + total.toFixed(2);
            } else {
                document.getElementById('total').value = '';
            }
        }
    </script>
</body>
</html>
