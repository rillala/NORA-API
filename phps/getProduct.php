<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

try {
    // 準備 SQL 語句
    $sql = "SELECT p.*, GROUP_CONCAT(DISTINCT pc.color) AS colors, GROUP_CONCAT(DISTINCT ps.size) AS sizes 
            FROM products p
            LEFT JOIN product_color pc ON p.product_id = pc.product_id
            LEFT JOIN product_size ps ON p.product_id = ps.product_id
            GROUP BY p.product_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // 獲取所有商品資料
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 假設 images 字段包含的是逗號分隔的圖片路徑字符串
    foreach ($products as $key => $product) {
        if (!empty($product['images'])) {
            // 將圖片路徑字符串分割成一個陣列
            $products[$key]['images'] = explode(',', $product['images']);
        } else {
            // 確保即使沒有圖片也返回一個空陣列
            $products[$key]['images'] = [];
        }
    }

    // 返回 JSON 格式的商品列表
    echo json_encode($products);
} catch (PDOException $e) {
    // 處理錯誤
    echo "錯誤：" . $e->getMessage();
}
?>
