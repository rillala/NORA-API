<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php"); // 确保这里的路径正确指向你的数据库连接文件

// 獲取 JSON 格式的 post 數據
$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'];
$order_status = $data['order_status'];

try {
    $pdo->beginTransaction(); // 开始事务

    // 准备 SQL 语句
    $sql = "UPDATE orders SET order_status = :order_status WHERE order_id = :order_id";
    $stmt = $pdo->prepare($sql);

    // 绑定参数
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':order_status', $order_status);

    // 执行 SQL 语句
    $stmt->execute();

    $pdo->commit(); // 提交事务

    // 返回成功消息
    echo json_encode(["success" => true, "message" => "訂單狀態更新成功"]);
} catch (PDOException $e) {
    $pdo->rollBack(); // 事务回滚
    // 返回错误消息
    echo json_encode(["success" => false, "message" => "訂單狀態更新失敗：" . $e->getMessage()]);
}
?>
