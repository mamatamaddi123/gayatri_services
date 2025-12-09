<?php
include("db.php");

$table = "stock_in";
$primaryKey = "id";

// Fetch dropdown data
$items = $conn->query("SELECT item_Id, item_Name FROM items WHERE stat='Active'");
$suppliers = $conn->query("SELECT supId, supName FROM suppliers");
$branches = $conn->query("SELECT branchId, branchName FROM branches");

// Handle CREATE or UPDATE
if(isset($_POST['create']) || isset($_POST['update'])){
    $isUpdate = isset($_POST['update']);
    if($isUpdate){
        $stmt = $conn->prepare("UPDATE $table SET item_id=?, quantity=?, stock_date=?, supplier_id=?, branch_id=? WHERE $primaryKey=?");
        $stmt->bind_param("iisiii", $_POST['item_id'], $_POST['quantity'], $_POST['stock_date'], $_POST['supplier_id'], $_POST['branch_id'], $_POST[$primaryKey]);
        $msg = "Updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO $table (item_id, quantity, stock_date, supplier_id, branch_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iisii", $_POST['item_id'], $_POST['quantity'], $_POST['stock_date'], $_POST['supplier_id'], $_POST['branch_id']);
        $msg = "Saved successfully!";
    }
    if($stmt->execute()){
        $_SESSION['message'] = $msg;
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=stock_in");
    exit;
}

// Fetch all stock_in records
$records = $conn->query("SELECT s.*, i.item_Name, sup.supName, b.branchName
                         FROM stock_in s
                         LEFT JOIN items i ON s.item_id=i.item_Id
                         LEFT JOIN suppliers sup ON s.supplier_id=sup.supId
                         LEFT JOIN branches b ON s.branch_id=b.branchId");

// Fetch record for edit
$editData = [];
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if($res) $editData = $res->fetch_assoc();
}
?>

<h2>Stock In</h2>
<div class="form-container">
<form id="stockInForm" method="POST">
    <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

    <label>Item:</label>
    <select name="item_id" id="item_id" required>
        <option value="">--Select Item--</option>
        <?php while($i = $items->fetch_assoc()): ?>
            <option value="<?= $i['item_Id'] ?>" <?= (isset($editData['item_id']) && $editData['item_id']==$i['item_Id'])?'selected':'' ?>><?= $i['item_Name'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="<?= $editData['quantity'] ?? '' ?>" required>

    <label>Date:</label>
    <input type="date" name="stock_date" id="stock_date" value="<?= $editData['stock_date'] ?? '' ?>" required>

    <label>Supplier:</label>
    <select name="supplier_id" id="supplier_id" required>
        <option value="">--Select Supplier--</option>
        <?php while($s = $suppliers->fetch_assoc()): ?>
            <option value="<?= $s['supId'] ?>" <?= (isset($editData['supplier_id']) && $editData['supplier_id']==$s['supId'])?'selected':'' ?>><?= $s['supName'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Branch:</label>
    <select name="branch_id" id="branch_id" required>
        <option value="">--Select Branch--</option>
        <?php while($b = $branches->fetch_assoc()): ?>
            <option value="<?= $b['branchId'] ?>" <?= (isset($editData['branch_id']) && $editData['branch_id']==$b['branchId'])?'selected':'' ?>><?= $b['branchName'] ?></option>
        <?php endwhile; ?>
    </select>

    <button type="submit" name="<?= $editData?'update':'create' ?>" id="submitBtn"><?= $editData?'Update':'Save' ?></button>
    <button type="button" id="viewStockInBtn">View</button>
</form>
</div>

<div id="stockInTableContainer" style="display:none; margin-top:20px;">
<h3>All Stock In Records</h3>
<table>
<tr>
    <th>ID</th><th>Item</th><th>Quantity</th><th>Date</th><th>Supplier</th><th>Branch</th><th>Action</th>
</tr>
<?php while($row = $records->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['item_Name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['stock_date'] ?></td>
    <td><?= $row['supName'] ?></td>
    <td><?= $row['branchName'] ?></td>
    <td>
        <a href="?page=stock_in&edit=<?= $row['id'] ?>" class="edit-btn">Edit</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
// Toggle Table View
document.getElementById("viewStockInBtn").addEventListener("click", function(){
    const table = document.getElementById("stockInTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
