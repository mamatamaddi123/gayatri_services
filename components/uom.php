<?php
include("db.php");

$table = "uom";
$primaryKey = "UOM_Id";

// Handle Save/Update
if(isset($_POST['create']) || isset($_POST['update'])){
    $isUpdate = isset($_POST['update']);
    if($isUpdate){
        $stmt = $conn->prepare("UPDATE $table SET UOM_Name=? WHERE $primaryKey=?");
        $stmt->bind_param("si", $_POST['UOM_Name'], $_POST[$primaryKey]);
        $msg = "Updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO $table (UOM_Name) VALUES (?)");
        $stmt->bind_param("s", $_POST['UOM_Name']);
        $msg = "Saved successfully!";
    }

    if($stmt->execute()){
        $_SESSION['message'] = $msg;
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=uom");
    exit;
}

// Fetch single record for edit
$editData = [];
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if($result) $editData = $result->fetch_assoc();
}

// Fetch all records
$records = $conn->query("SELECT * FROM $table");
?>

<h2>UOM Master</h2>
<div class="form-container">
<form method="POST" id="uomForm">
    <input type="hidden" name="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">
    
    <label>UOM Name</label>
    <input type="text" name="UOM_Name" value="<?= $editData['UOM_Name'] ?? '' ?>" required>
    
    <button type="submit" name="<?= $editData?'update':'create' ?>" id="saveBtn"><?= $editData?'Update':'Save' ?></button>
    <button type="button" id="viewUOMBtn">View</button>
</form>
</div>

<div id="uomTableContainer" style="display:none; margin-top:20px;">
<h3>All UOMs</h3>
<table border="1" width="100%" cellspacing="0" cellpadding="8">
<tr>
<th>ID</th><th>UOM Name</th><th>Actions</th>
</tr>
<?php while($row=$records->fetch_assoc()): ?>
<tr>
<td><?= $row[$primaryKey] ?></td>
<td><?= htmlspecialchars($row['UOM_Name']) ?></td>
<td>
<a href="?page=uom&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
// Toggle table view
document.getElementById("viewUOMBtn").addEventListener("click", function(){
    const table = document.getElementById("uomTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
