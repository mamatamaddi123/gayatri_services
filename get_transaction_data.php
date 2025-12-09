<?php
include("db.php");

if (isset($_GET['docNo'])) {
    $docNo = $_GET['docNo'];

    try {
        // Fetch document header
        $stmt = $conn->prepare("
            SELECT Trans_Docs_No, Trans_date, custId, remarks, flag
            FROM trans_head
            WHERE Trans_Docs_No = ? AND flag = 'IN'
        ");
        $stmt->execute([$docNo]);
        $head = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($head) {
            // Fetch line items
            $stmt2 = $conn->prepare("
                SELECT item_Code, qty, rate, total
                FROM trans_line
                WHERE Trans_Docs_No = ? AND flag = 'IN'
            ");
            $stmt2->execute([$docNo]);
            $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'document' => $head,
                'items' => $items
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Document not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
