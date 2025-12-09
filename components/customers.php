<?php
include("db.php");

$table = "customers";
$primaryKey = "custId";

// CREATE
if (isset($_POST['create'])) {
    $stmt = $conn->prepare("INSERT INTO $table (custName, custAdd, cust_phoneNo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['custName'], $_POST['custAdd'], $_POST['cust_phoneNo']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Saved successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=customers");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE $table SET custName=?, custAdd=?, cust_phoneNo=? WHERE $primaryKey=?");
    $stmt->bind_param("sssi", $_POST['custName'], $_POST['custAdd'], $_POST['cust_phoneNo'], $_POST[$primaryKey]);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Updated successfully!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while updating!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=customers");
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
$records = $conn->query("SELECT * FROM $table");
?>

<h2>Customers Master</h2>

<div class="form-container">
    <form method="POST">
        <input type="hidden" name="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

        <label>Customer Name</label>
        <input type="text" name="custName" value="<?= $editData['custName'] ?? '' ?>" required>

        <label>Customer Address</label>
        <input type="text" name="custAdd" value="<?= $editData['custAdd'] ?? '' ?>" required>

        <label>Phone Number</label>
        <input type="text" name="cust_phoneNo" value="<?= $editData['cust_phoneNo'] ?? '' ?>" required>

        <button type="submit" name="<?= $editData ? 'update' : 'create' ?>" id="saveBtn">
            <?= $editData ? 'Update' : 'Save' ?>
        </button>
        <button type="button" id="viewCustomersBtn">View</button>
    </form>
</div>

<div id="customersTableContainer" style="display:none; margin-top:20px;">
    <h3>All Customers</h3>
    <table border="1" width="100%" cellspacing="0" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $records->fetch_assoc()): ?>
            <tr>
                <td><?= $row[$primaryKey] ?></td>
                <td><?= htmlspecialchars($row['custName']) ?></td>
                <td><?= htmlspecialchars($row['custAdd']) ?></td>
                <td><?= htmlspecialchars($row['cust_phoneNo']) ?></td>
                <td>
                    <a href="?page=customers&edit=<?= $row[$primaryKey] ?>" class="edit-btn">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
document.getElementById("viewCustomersBtn").addEventListener("click", function(){
    const table = document.getElementById("customersTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
