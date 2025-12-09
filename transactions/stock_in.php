<?php
session_start();
include("../db.php");

$table = "trans_head";
$primaryKey = "Trans_Head_Id";

// Fetch dropdown data
$items = $conn->query("SELECT item_Id, item_Name FROM items WHERE stat='Active'");
$suppliers = $conn->query("SELECT supId, supName FROM suppliers");
$branches = $conn->query("SELECT branchId, branchName FROM branch");

// Handle CREATE or UPDATE
if(isset($_POST['create']) || isset($_POST['update'])){
    $isUpdate = isset($_POST['update']);
    $item_code = $conn->query("SELECT item_Code FROM items WHERE item_Id={$_POST['item_id']}")->fetch_assoc()['item_Code'];
    $remarks = 'Supplier ID: ' . $_POST['supplier_id'];
    if($isUpdate){
        $trans_docs_no = $_POST['trans_docs_no'];
        $stmt = $conn->prepare("UPDATE trans_head SET Trans=?, branchId=?, remarks=? WHERE $primaryKey=?");
        $stmt->bind_param("sisi", $_POST['stock_date'], $_POST['branch_id'], $remarks, $_POST[$primaryKey]);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE trans_line SET item_Code=?, qty=? WHERE Trans_Docs_No=?");
        $stmt->bind_param("sis", $item_code, $_POST['quantity'], $trans_docs_no);
        $msg = "Updated successfully!";
    } else {
        $trans_docs_no = 'SI-' . date('YmdHis');
        $stmt = $conn->prepare("INSERT INTO trans_head (Trans_Docs_No, Trans, custId, flag, remarks, userId, branchId) VALUES (?, ?, NULL, 'in', ?, 1, ?)");
        $stmt->bind_param("sssi", $trans_docs_no, $_POST['stock_date'], $remarks, $_POST['branch_id']);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO trans_line (Trans_Docs_No, item_Code, qty, total, flag) VALUES (?, ?, ?, 0, 'in')");
        $stmt->bind_param("ssi", $trans_docs_no, $item_code, $_POST['quantity']);
        $msg = "Saved successfully!";
    }
    if($stmt->execute()){
        $_SESSION['message'] = $msg;
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: transactions/stock_in.php");
    exit;
}

// Fetch all stock_in records
$records = $conn->query("SELECT th.Trans_Head_Id as id, th.Trans_Docs_No, th.Trans as stock_date, tl.qty as quantity, tl.item_Code, i.item_Name, b.branchName, th.remarks
                         FROM trans_head th
                         LEFT JOIN trans_line tl ON th.Trans_Docs_No = tl.Trans_Docs_No
                         LEFT JOIN items i ON tl.item_Code = i.item_Code
                         LEFT JOIN branch b ON th.branchId = b.branchId
                         WHERE th.flag='in'");

// Fetch record for edit
$editData = [];
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT th.*, tl.qty, tl.item_Code, i.item_Id FROM trans_head th LEFT JOIN trans_line tl ON th.Trans_Docs_No = tl.Trans_Docs_No LEFT JOIN items i ON tl.item_Code = i.item_Code WHERE th.$primaryKey=$id");
    if($res) $editData = $res->fetch_assoc();
    $editData['trans_docs_no'] = $editData['Trans_Docs_No'];
    $supplier_id = str_replace('Supplier ID: ', '', $editData['remarks']);
    $editData['supplier_id'] = $supplier_id;
}
?>

<h2>Stock In</h2>
<div class="form-container">
<form id="stockInForm" method="POST">
    <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">
    <input type="hidden" name="trans_docs_no" value="<?= $editData['trans_docs_no'] ?? '' ?>">

    <label>Item:</label>
    <select name="item_id" id="item_id" required>
        <option value="">--Select Item--</option>
        <?php while($i = $items->fetch_assoc()): ?>
            <option value="<?= $i['item_Id'] ?>" <?= (isset($editData['item_Id']) && $editData['item_Id']==$i['item_Id'])?'selected':'' ?>><?= $i['item_Name'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="<?= $editData['qty'] ?? '' ?>" required>

    <label>Date:</label>
    <input type="date" name="stock_date" id="stock_date" value="<?= $editData['Trans'] ?? '' ?>" required>

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
            <option value="<?= $b['branchId'] ?>" <?= (isset($editData['branchId']) && $editData['branchId']==$b['branchId'])?'selected':'' ?>><?= $b['branchName'] ?></option>
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
<?php while($row = $records->fetch_assoc()): 
    $supplier_id = str_replace('Supplier ID: ', '', $row['remarks']);
    $supName = $conn->query("SELECT supName FROM suppliers WHERE supId=$supplier_id")->fetch_assoc()['supName'];
?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['item_Name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['stock_date'] ?></td>
    <td><?= $supName ?></td>
    <td><?= $row['branchName'] ?></td>
    <td>
        <a href="?edit=<?= $row['id'] ?>" class="edit-btn">Edit</a>
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
