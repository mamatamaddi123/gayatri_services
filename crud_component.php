<?php
function renderTable($conn, $table, $fields, $primaryKey) {
    $result = $conn->query("SELECT * FROM $table");
    if (!$result) { echo "Error: ".$conn->error; return; }

    echo "<div class='table-container'>";
    echo "<table>";
    echo "<tr><th>$primaryKey</th>";
    foreach ($fields as $field) echo "<th>".ucfirst($field)."</th>";
    echo "<th>Actions</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row[$primaryKey]}</td>";
        foreach ($fields as $field) echo "<td>{$row[$field]}</td>";
        echo "<td>
            <a class='action-btn edit-btn' href='?page=branches&edit={$row[$primaryKey]}'>Edit</a>
        </td>";
        echo "</tr>";
    }

    echo "</table></div>";
}

function handleCRUD($conn, $table, $fields, $primaryKey) {
    // CREATE
    if (isset($_POST['create'])) {
        $cols = implode(", ", $fields);
        $placeholders = implode(", ", array_fill(0, count($fields), "?"));
        $types = str_repeat("s", count($fields));

        $stmt = $conn->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)");
        $stmt->bind_param($types, ...array_map(fn($f) => $_POST[$f], $fields));
        $stmt->execute();
    }

    // UPDATE
    if (isset($_POST['update'])) {
        $id = $_POST[$primaryKey];
        $set = implode(", ", array_map(fn($f) => "$f=?", $fields));
        $types = str_repeat("s", count($fields)) . "i";

        $stmt = $conn->prepare("UPDATE $table SET $set WHERE $primaryKey=?");
        $values = array_map(fn($f) => $_POST[$f], $fields);
        $values[] = $id;
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
    }
}
?>
