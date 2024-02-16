<?php
header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

try {
    // 準備 SQL 語句
    $sql = "SELECT * FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // 獲取所有商品資料
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 返回 JSON 格式的商品列表
    header("Content-Type: application/json");
    echo json_encode($products);
} catch (PDOException $e) {
    // 處理錯誤
    echo "錯誤：" . $e->getMessage();
}
?>
