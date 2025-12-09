<?php
include("db.php");

$table = "items";
$primaryKey = "item_Id";

// Fetch dropdown data
$categories = $conn->query("SELECT catgId, catgName FROM category");
$brands = $conn->query("SELECT brandId, brandName FROM brand");
$uoms = $conn->query("SELECT UOM_Id, UOM_Name FROM uom");

// Store UOMs in array
$uomsArr = [];
while($u = $uoms->fetch_assoc()){
    $uomsArr[] = $u;
}

// Handle CREATE or UPDATE
if(isset($_POST['create']) || isset($_POST['update'])){
    $isUpdate = isset($_POST['update']);
    if($isUpdate){
        $stmt = $conn->prepare("UPDATE $table SET item_Code=?, item_Name=?, UOM_Id=?, catgId=?, brandId=?, price=?, rack_No=?, `stat`=? WHERE $primaryKey=?");
        $stmt->bind_param("ssiiidssi", $_POST['item_Code'], $_POST['item_Name'], $_POST['uomId'], $_POST['catgId'], $_POST['brandId'], $_POST['price'], $_POST['rack_No'], $_POST['stat'], $_POST[$primaryKey]);
        $msg = "Updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO $table (item_Code,item_Name,UOM_Id,catgId,brandId,price,rack_No,`stat`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssiiidss", $_POST['item_Code'], $_POST['item_Name'], $_POST['uomId'], $_POST['catgId'], $_POST['brandId'], $_POST['price'], $_POST['rack_No'], $_POST['stat']);
        $msg = "Saved successfully!";
    }
    if($stmt->execute()){
        $_SESSION['message'] = $msg;
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Error while saving!";
        $_SESSION['alert_type'] = "error";
    }
    header("Location: admin.php?page=items");
    exit;
}

// Fetch all items
$records = $conn->query("SELECT i.*, c.catgName, b.brandName, u.UOM_Name 
                         FROM items i 
                         LEFT JOIN category c ON i.catgId=c.catgId
                         LEFT JOIN brand b ON i.brandId=b.brandId
                         LEFT JOIN uom u ON i.UOM_Id=u.UOM_Id");

// Fetch record for edit
$editData = [];
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM $table WHERE $primaryKey=$id");
    if($res) $editData = $res->fetch_assoc();
}
?>

<h2>Items Master</h2>
<div class="form-container">
<form id="itemForm" method="POST">
    <input type="hidden" name="<?= $primaryKey ?>" id="<?= $primaryKey ?>" value="<?= $editData[$primaryKey] ?? '' ?>">

    <label>Item Code:</label>
    <input type="text" name="item_Code" id="item_Code" value="<?= $editData['item_Code'] ?? '' ?>" required>

    <label>Item Name:</label>
    <input type="text" name="item_Name" id="item_Name" value="<?= $editData['item_Name'] ?? '' ?>" required>

    <label>UOM:</label>
    <select name="uomId" id="uomId" required>
        <option value="">--Select UOM--</option>
        <?php foreach($uomsArr as $u): ?>
            <option value="<?= $u['UOM_Id'] ?>" <?= (isset($editData['UOM_Id']) && $editData['UOM_Id']==$u['UOM_Id'])?'selected':'' ?>><?= $u['UOM_Name'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Category:</label>
    <select name="catgId" id="catgId" required>
        <option value="">--Select Category--</option>
        <?php while($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['catgId'] ?>" <?= (isset($editData['catgId']) && $editData['catgId']==$c['catgId'])?'selected':'' ?>><?= $c['catgName'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Brand:</label>
    <select name="brandId" id="brandId" required>
        <option value="">--Select Brand--</option>
        <?php while($b = $brands->fetch_assoc()): ?>
            <option value="<?= $b['brandId'] ?>" <?= (isset($editData['brandId']) && $editData['brandId']==$b['brandId'])?'selected':'' ?>><?= $b['brandName'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Price:</label>
    <input type="text" name="price" id="price" value="<?= $editData['price'] ?? '' ?>" required>

    <label>Rack No:</label>
    <input type="text" name="rack_No" id="rack_No" value="<?= $editData['rack_No'] ?? '' ?>" required>

    <label>Status:</label>
    <select name="stat" id="stat" required>
        <option value="">--Select Status--</option>
        <option value="Active" <?= (isset($editData['stat']) && $editData['stat']=="Active")?'selected':'' ?>>Active</option>
        <option value="Close" <?= (isset($editData['stat']) && $editData['stat']=="Close")?'selected':'' ?>>Close</option>
    </select>

    <button type="submit" name="<?= $editData?'update':'create' ?>" id="submitBtn"><?= $editData?'Update':'Save' ?></button>
    <button type="button" id="viewItemsBtn">View</button>
</form>
</div>

<div id="itemsTableContainer" style="display:none; margin-top:20px;">
<h3>All Items</h3>
<table>
<tr>
    <th>ID</th><th>Code</th><th>Name</th><th>UOM</th><th>Category</th><th>Brand</th><th>Price</th><th>Rack No</th><th>Status</th><th>Action</th>
</tr>
<?php while($row = $records->fetch_assoc()): ?>
<tr>
    <td><?= $row['item_Id'] ?></td>
    <td><?= $row['item_Code'] ?></td>
    <td><?= $row['item_Name'] ?></td>
    <td><?= $row['UOM_Name'] ?></td>
    <td><?= $row['catgName'] ?></td>
    <td><?= $row['brandName'] ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['rack_No'] ?></td>
    <td><?= $row['stat'] ?></td>
    <td>
        <a href="?page=items&edit=<?= $row['item_Id'] ?>" class="edit-btn">Edit</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
// Toggle Table View
document.getElementById("viewItemsBtn").addEventListener("click", function(){
    const table = document.getElementById("itemsTableContainer");
    if(table.style.display === "none" || table.style.display === ""){
        table.style.display = "block";
        this.textContent = "Hide";
    } else {
        table.style.display = "none";
        this.textContent = "View";
    }
});
</script>
