<?php
include("db.php");

$table = "suppliers";
$primaryKey = "supId";

// Handle Save
if(isset($_POST['create']) || isset($_POST['update'])){
    $isUpdate = isset($_POST['update']);
    if($isUpdate){
        $stmt = $conn->prepare("UPDATE $table SET supName=?, supAdd=?, phoneNo=? WHERE $primaryKey=?");
        $stmt->bind_param("sssi", $_POST['supName'], $_POST['supAdd'], $_POST['phoneNo'], $_POST[$primaryKey]);
        $msg = "Updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO $table (supName,supAdd,phoneNo) VALUES (?,?,?)");
        $stmt->bind_param("sss", $_POST['supName'], $_POST['supAdd'], $_POST['phoneNo']);
        $msg = "Saved successfully!";
    }
    if($stmt->execute()){
        $_SESSION['message'] = $msg;
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=suppliers");
    exit;
}

// Fetch all records
$records = $conn->query("SELECT * FROM $table");

// Fetch record for edit
$editData = [];
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if($res) $editData = $res->fetch_assoc();
}
?>

<h2>Suppliers Master</h2>
<div class="form-container">
    <form method="POST" id="supplierForm">
        <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

        <label>Supplier Name</label>
        <input type="text" name="supName" id="supName" value="<?= $editData['supName'] ?? '' ?>" required>

        <label>Supplier Address</label>
        <input type="text" name="supAdd" id="supAdd" value="<?= $editData['supAdd'] ?? '' ?>" required>

        <label>Phone Number</label>
        <input type="text" name="phoneNo" id="phoneNo" value="<?= $editData['phoneNo'] ?? '' ?>" required>

        <button type="submit" name="<?= $editData?'update':'create' ?>" id="saveBtn"><?= $editData?'Update':'Save' ?></button>
        <button type="button" id="viewBtn">View</button>
    </form>
</div>

<div class="table-container" id="tableContainer" style="display:none; margin-top:20px;">
    <h3>All Suppliers</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $records->fetch_assoc()): ?>
        <tr>
            <td><?= $row['supId'] ?></td>
            <td><?= htmlspecialchars($row['supName']) ?></td>
            <td><?= htmlspecialchars($row['supAdd']) ?></td>
            <td><?= htmlspecialchars($row['phoneNo']) ?></td>
            <td>
                <a href="?page=suppliers&edit=<?= $row['supId'] ?>" class="edit-btn">Edit</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
// Toggle table view
document.getElementById("viewBtn").addEventListener("click", function(){
    const table = document.getElementById("tableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
