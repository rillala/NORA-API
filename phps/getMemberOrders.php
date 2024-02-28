<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

// 假设通过GET请求传递了member_id
$memberId = isset($_GET['member_id']) ? $_GET['member_id'] : '';
error_log("Received member_id: " . $memberId); // 查看日志以确认接收到的 member_id

try {
    // 修改SQL语句，添加WHERE子句以便根据member_id过滤订单
    $sql = "SELECT o.order_id, o.name, o.order_date, o.total_amount, o.delivery_method, o.payment, o.order_status,
            GROUP_CONCAT(DISTINCT od.color) AS colors, GROUP_CONCAT(DISTINCT od.size) AS sizes
            FROM orders o
            LEFT JOIN order_details od ON o.order_id = od.order_id
            WHERE o.member_id = :memberId
            GROUP BY o.order_id";

    $stmt = $pdo->prepare($sql);
    // 绑定member_id参数
    $stmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
    $stmt->execute();

    // 获取所有相关订单数据
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 处理colors和sizes字段
    foreach ($orders as $key => $order) {
        $orders[$key]['colors'] = !empty($order['colors']) ? explode(',', $order['colors']) : [];
        $orders[$key]['sizes'] = !empty($order['sizes']) ? explode(',', $order['sizes']) : [];
    }

    // 返回JSON格式的订单列表
    echo json_encode($orders);
} catch (PDOException $e) {
    // 处理错误
    echo "错误：" . $e->getMessage();
}
?>
