<?php
require_once("./connect_chd104g1.php");

// 接收前端發送的商品資訊
$productData = json_decode(file_get_contents("php://input"));

// 準備 SQL 語句
$sql = "INSERT INTO products (images, title, category, description, price, state, createdate) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

// 執行 SQL 語句並綁定參數
$stmt->execute([$productData->images, $productData->title, $productData->category, $productData->description, $productData->price, $productData->state, $productData->createdate]);


// 返回成功或失敗的訊息給前端
if ($stmt) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false]);
}
?>
