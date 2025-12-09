<?php
include("db.php");  // Only DB connection

$table = "branch";
$primaryKey = "branchId";

// CREATE
if (isset($_POST['create'])) {
    $stmt = $conn->prepare("INSERT INTO $table (branchName, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['branchName'], $_POST['location']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Saved successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=branches");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE $table SET branchName=?, location=? WHERE $primaryKey=?");
    $stmt->bind_param("ssi", $_POST['branchName'], $_POST['location'], $_POST[$primaryKey]);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Updated successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while updating!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=branches");
    exit;
}

// FETCH SINGLE RECORD FOR EDIT
$editData = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if ($result) $editData = $result->fetch_assoc();
}

// FETCH ALL RECORDS
$branches = $conn->query("SELECT * FROM $table");
?>

<h2>Branches Master</h2>

<div class="form-container">
    <form method="POST" id="branchForm">
        <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

        <label>Branch Name</label>
        <input type="text" name="branchName" id="branchName" value="<?= $editData['branchName'] ?? '' ?>" required>

        <label>Location</label>
        <input type="text" name="location" id="location" value="<?= $editData['location'] ?? '' ?>" required>

        <button type="submit" name="<?= $editData ? 'update' : 'create' ?>" id="saveBtn">
            <?= $editData ? 'Update' : 'Save' ?>
        </button>
        <button type="button" id="viewBranchesBtn">View</button>
    </form>
</div>

<div id="branchesTableContainer" style="display:none; margin-top:20px;">
    <h3>All Branches</h3>
    <table border="1" width="100%" cellspacing="0" cellpadding="8">
        <tr>
            <th>ID</th><th>Branch Name</th><th>Location</th><th>Actions</th>
        </tr>
        <?php while($row = $branches->fetch_assoc()): ?>
            <tr>
                <td><?= $row[$primaryKey] ?></td>
                <td><?= $row['branchName'] ?></td>
                <td><?= $row['location'] ?></td>
                <td>
                    <a href="?page=branches&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
document.getElementById("viewBranchesBtn").addEventListener("click", function(){
    const table = document.getElementById("branchesTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";      // show table
        this.textContent = "Hide";           // change button text
    } else {
        table.style.display = "none";       // hide table
        this.textContent = "View";          // reset button text
    }
});
</script>
