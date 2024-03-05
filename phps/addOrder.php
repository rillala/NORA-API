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
    $sql = "INSERT INTO orders (member_id, name, phone, email, address, delivery_method, payment, total_amount, order_status, comment, order_date, delivery_fee) VALUES (:member_id, :name, :phone, :email, :address, :delivery_method, :payment, :total_amount, :order_status, :comment, NOW(), :delivery_fee)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':member_id' => $data['member_id'],
        ':name' => $data['name'],
        ':phone' => $data['phone'],
        ':email' => $data['email'],
        ':address' => $data['address'],
        ':delivery_method' => $data['delivery_method'],
        ':payment' => $data['payment'],
        ':total_amount' => $data['total_amount'],
        ':order_status' => $data['order_status'],
        ':comment' => $data['comment'],
        ':delivery_fee' => $data['delivery_fee']
    ]);
    
    $order_id = $pdo->lastInsertId(); // 獲取剛插入的訂單ID

    foreach ($data['cartList']['carts'] as $item) {
        $product_id = $item['productId'];
        $quantity = $item['qty'];
        $unit_price = $item['product']['price'];
        $subtotal = $item['subtotal'];
        $size = $item['selectedSize'];
        $color = $item['selectedColor'];
        $title = $item['product']['title'];
    
        // 准备插入 order_details 表的 SQL 语句
        $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal, size, color, title) VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal, :size, :color, :title)";
        $stmt_detail = $pdo->prepare($sql_detail);
        
        // 执行插入操作
        $stmt_detail->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':unit_price' => $unit_price,
            ':subtotal' => $subtotal,
            ':size' => $size,
            ':color' => $color,
            ':title' => $title,
        ]);
    }
    // 根據需要處理 cartList 中的商品信息...

    $pdo->commit(); // 提交事務
    echo json_encode(["success" => true, "message" => "訂單已成功添加", "order_id" => $order_id]);
} catch (PDOException $e) {
    $pdo->rollBack(); // 回滾事務
    echo json_encode(["success" => false, "message" => "訂單添加失敗：" . $e->getMessage()]);
}
?>
