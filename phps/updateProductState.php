<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'];
$state = $data['state'];

// SQL 更新语句
$sql = "UPDATE products SET state = ? WHERE product_id = ?";
$stmt = $pdo->prepare($sql);

// 执行更新
if ($stmt->execute([$state, $product_id])) {
    echo json_encode(["success" => true, "message" => "商品状态更新成功"]);
} else {
    echo json_encode(["success" => false, "message" => "商品状态更新失败"]);
}
