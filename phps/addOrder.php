<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

// 獲取前端發送的數據
$data = json_decode(file_get_contents("php://input"), true);

try {
    $pdo->beginTransaction(); // 開始事務

    // 插入訂單基本資訊
    $sql = "INSERT INTO orders (member_id, name, phone, email, address, delivery_method, payment, total_amount, order_status, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['member_id'], $data['name'], $data['phone'], $data['email'], $data['address'], $data['delivery_method'], $data['payment'], $data['total_amount'], $data['order_status'], $data['comment']]);
    
    $order_id = $pdo->lastInsertId(); // 獲取剛插入的訂單ID

    // 根據需要處理 cartList 中的商品信息...

    $pdo->commit(); // 提交事務
    echo json_encode(["success" => true, "message" => "訂單已成功添加", "order_id" => $order_id]);
} catch (PDOException $e) {
    $pdo->rollBack(); // 回滾事務
    echo json_encode(["success" => false, "message" => "訂單添加失敗：" . $e->getMessage()]);
}
?>
