<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

$product_id = isset($_GET['id']) ? $_GET['id'] : '';

try {
    if (!empty($product_id)) {
        $sql = "SELECT p.*, GROUP_CONCAT(DISTINCT pc.color) AS colors, GROUP_CONCAT(DISTINCT ps.size) AS sizes 
                FROM products p
                LEFT JOIN product_color pc ON p.product_id = pc.product_id
                LEFT JOIN product_size ps ON p.product_id = ps.product_id
                WHERE p.product_id = :product_id
                GROUP BY p.product_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if (!empty($product['images'])) {
                $product['images'] = explode(',', $product['images']);
            } else {
                $product['images'] = [];
            }
            echo json_encode($product);
        } else {
            echo json_encode(array("error" => "Product not found."));
        }
    } else {
        echo json_encode(array("error" => "Product ID is required."));
    }
} catch (PDOException $e) {
    echo "錯誤：" . $e->getMessage();
}
?>
