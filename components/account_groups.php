<?php
include("db.php");  // Only DB connection; no session_start() or crud_component.php

$table = "account_groups";
$primaryKey = "Acc_Id";

// CREATE
if (isset($_POST['create'])) {
    $stmt = $conn->prepare("INSERT INTO $table (Acc_Name) VALUES (?)");
    $stmt->bind_param("s", $_POST['Acc_Name']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Saved successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=account_groups");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE $table SET Acc_Name=? WHERE $primaryKey=?");
    $stmt->bind_param("si", $_POST['Acc_Name'], $_POST[$primaryKey]);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Updated successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while updating!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=account_groups");
    exit;
}

// FETCH DATA FOR EDIT
$editData = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if ($result) $editData = $result->fetch_assoc();
}

// FETCH ALL RECORDS
$records = $conn->query("SELECT * FROM $table");
?>

<h2>Account Groups Master</h2>
<div class="form-container">
  <form method="POST">
    <input type="hidden" name="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">
    <label>Account Group Name</label>
    <input type="text" name="Acc_Name" value="<?= $editData['Acc_Name'] ?? '' ?>" required>
    
    <button type="submit" name="<?= $editData ? 'update' : 'create' ?>">
      <?= $editData ? 'Update' : 'Save' ?>
    </button>
    <button type="button" id="viewAccBtn">View</button>
  </form>
</div>

<div id="accTableContainer" style="display:none; margin-top:20px;">
  <h3>All Account Groups</h3>
  <table border="1" width="100%" cellspacing="0" cellpadding="8">
    <tr>
      <th>ID</th>
      <th>Account Group Name</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $records->fetch_assoc()): ?>
    <tr>
      <td><?= $row[$primaryKey] ?></td>
      <td><?= $row['Acc_Name'] ?></td>
      <td>
        <a href="?page=account_groups&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<script>
document.getElementById("viewAccBtn").addEventListener("click", function(){
    const table = document.getElementById("accTableContainer");
    if (table.style.display === "none" || table.style.display === "") {
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
