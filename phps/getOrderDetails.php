<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

// 假設通過GET方法獲取訂單ID
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

try {
    // 聯合查詢orders表和order_details表，獲取訂單詳細資訊
    $sql = "SELECT *
            FROM orders o
            LEFT JOIN order_details od ON o.order_id = od.order_id
            WHERE o.order_id = :order_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);

    // 獲取查詢結果
    $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 返回JSON格式的訂單詳細資訊
    echo json_encode($orderDetails);
} catch (PDOException $e) {
    // 處理錯誤
    echo json_encode(["error" => true, "message" => "訂單詳細資訊獲取失敗：" . $e->getMessage()]);
}
?>
