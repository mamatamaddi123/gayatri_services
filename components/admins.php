<?php
include("db.php");  // Only DB connection

$table = "users";
$primaryKey = "userId";

// CREATE
if (isset($_POST['create'])) {
    $hashedPassword = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO $table (userName, pwd, role, branchId) VALUES (?, ?, 'admin', ?)");
    $stmt->bind_param("ssi", $_POST['userName'], $hashedPassword, $_POST['branchId']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Admin created successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while creating admin!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=admins");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    if (!empty($_POST['pwd'])) {
        // Update with new password
        $hashedPassword = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE $table SET userName=?, pwd=?, branchId=? WHERE $primaryKey=?");
        $stmt->bind_param("ssii", $_POST['userName'], $hashedPassword, $_POST['branchId'], $_POST[$primaryKey]);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE $table SET userName=?, branchId=? WHERE $primaryKey=?");
        $stmt->bind_param("sii", $_POST['userName'], $_POST['branchId'], $_POST[$primaryKey]);
    }
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Admin updated successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while updating admin!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=admins");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM $table WHERE $primaryKey=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Admin deleted successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while deleting admin!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=admins");
    exit;
}

// FETCH SINGLE RECORD FOR EDIT
$editData = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id AND role='admin'");
    if ($result) $editData = $result->fetch_assoc();
}

// FETCH ALL ADMIN RECORDS WITH BRANCH NAMES
$admins = $conn->query("
    SELECT u.*, b.branchName 
    FROM $table u 
    LEFT JOIN branch b ON u.branchId = b.branchId 
    WHERE u.role = 'admin'
");

// FETCH ALL BRANCHES FOR DROPDOWN
$branches = $conn->query("SELECT * FROM branch");
?>

<h2>Admin Management</h2>

<div class="form-container">
    <form method="POST" id="adminForm">
        <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

        <label>Username</label>
        <input type="text" name="userName" id="userName" value="<?= $editData['userName'] ?? '' ?>" required>

        <label>Password <?= $editData ? '(Leave blank to keep current password)' : '' ?></label>
        <input type="password" name="pwd" id="pwd" <?= $editData ? '' : 'required' ?>>

        <label>Assigned Branch</label>
        <select name="branchId" id="branchId" required>
            <option value="">Select Branch</option>
            <?php 
            $branches->data_seek(0); // Reset pointer
            while($branch = $branches->fetch_assoc()): 
            ?>
                <option value="<?= $branch['branchId'] ?>" 
                    <?= ($editData['branchId'] ?? '') == $branch['branchId'] ? 'selected' : '' ?>>
                    <?= $branch['branchName'] ?> - <?= $branch['location'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="<?= $editData ? 'update' : 'create' ?>" id="saveBtn">
            <?= $editData ? 'Update' : 'Create Admin' ?>
        </button>
        <button type="button" id="viewAdminsBtn">View Admins</button>
        <?php if ($editData): ?>
            <button type="button" onclick="window.location.href='admin.php?page=admins'" class="cancel-btn">Cancel</button>
        <?php endif; ?>
    </form>
</div>

<div id="adminsTableContainer" style="display:none; margin-top:20px;">
    <h3>All Admins</h3>
    <table border="1" width="100%" cellspacing="0" cellpadding="8">
        <tr>
            <th>ID</th><th>Username</th><th>Assigned Branch</th><th>Actions</th>
        </tr>
        <?php while($row = $admins->fetch_assoc()): ?>
            <tr>
                <td><?= $row[$primaryKey] ?></td>
                <td><?= $row['userName'] ?></td>
                <td><?= $row['branchName'] ?? 'No Branch Assigned' ?></td>
                <td>
                    <a href="?page=admins&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
                    <a href="?page=admins&delete=<?= $row[$primaryKey] ?>" 
                       class="delete-btn" 
                       onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
document.getElementById("viewAdminsBtn").addEventListener("click", function(){
    const table = document.getElementById("adminsTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";      // show table
        this.textContent = "Hide";           // change button text
    } else {
        table.style.display = "none";       // hide table
        this.textContent = "View Admins";   // reset button text
    }
});
</script>