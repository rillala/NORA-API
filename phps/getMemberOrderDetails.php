<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php"); // 调整为您的数据库连接文件

// 确保传入了 order_id
if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        $sql = "SELECT od.title, od.color, od.size, od.quantity, od.unit_price, od.subtotal
                FROM order_details od
                JOIN orders o ON od.order_id = o.order_id
                WHERE o.order_id = :order_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 将查询结果返回给客户端
        echo json_encode($orderDetails);
    } catch(PDOException $e) {
        // 处理错误
        echo "错误：" . $e->getMessage();
    }
} else {
    echo json_encode(["error" => true, "message" => "缺少订单ID"]);
}
?>
