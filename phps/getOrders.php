<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

try {
    // 準備 SQL 語句
    $sql = "SELECT o.order_id, o.name, o.order_date, o.total_amount, o.delivery_method, o.payment, o.order_status,
            GROUP_CONCAT(DISTINCT od.color) AS colors, GROUP_CONCAT(DISTINCT od.size) AS sizes
            FROM orders o
            LEFT JOIN order_details od ON o.order_id = od.order_id
            GROUP BY o.order_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // 獲取所有訂單資料
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 對於 colors 和 sizes 字段的處理，如果您的訂單詳情中包含這些資訊
    foreach ($orders as $key => $order) {
        if (!empty($order['colors'])) {
            // 將顏色列表從字符串分割成陣列
            $orders[$key]['colors'] = explode(',', $order['colors']);
        } else {
            $orders[$key]['colors'] = [];
        }
        if (!empty($order['sizes'])) {
            // 將尺寸列表從字符串分割成陣列
            $orders[$key]['sizes'] = explode(',', $order['sizes']);
        } else {
            $orders[$key]['sizes'] = [];
        }
    }

    // 返回 JSON 格式的訂單列表
    echo json_encode($orders);
} catch (PDOException $e) {
    // 處理錯誤
    echo "錯誤：" . $e->getMessage();
}
?>
