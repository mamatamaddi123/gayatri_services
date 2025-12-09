<?php
include("db.php");

$table = "category";
$primaryKey = "catgId";

// CREATE
if (isset($_POST['create'])) {
    $stmt = $conn->prepare("INSERT INTO $table (catgName) VALUES (?)");
    $stmt->bind_param("s", $_POST['catgName']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Saved successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=category");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE $table SET catgName=? WHERE $primaryKey=?");
    $stmt->bind_param("si", $_POST['catgName'], $_POST[$primaryKey]);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Updated successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while updating!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=category");
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
$categories = $conn->query("SELECT * FROM $table");
?>

<h2>Category Master</h2>

<div class="form-container">
    <form method="POST">
        <input type="hidden" name="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

        <label>Category Name</label>
        <input type="text" name="catgName" value="<?= $editData['catgName'] ?? '' ?>" required>

        <button type="submit" name="<?= $editData ? 'update' : 'create' ?>">
            <?= $editData ? 'Update' : 'Save' ?>
        </button>
        <button type="button" id="viewCategoryBtn">View</button>
    </form>
</div>

<div id="categoryTableContainer" style="display:none; margin-top:20px;">
    <h3>All Categories</h3>
    <table border="1" width="100%" cellspacing="0" cellpadding="8">
        <tr>
            <th>ID</th><th>Category Name</th><th>Actions</th>
        </tr>
        <?php while($row = $categories->fetch_assoc()): ?>
            <tr>
                <td><?= $row[$primaryKey] ?></td>
                <td><?= $row['catgName'] ?></td>
                <td>
                    <a href="?page=category&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
document.getElementById("viewCategoryBtn").addEventListener("click", function(){
    const table = document.getElementById("categoryTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
