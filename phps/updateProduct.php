<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

// 接收前端发送的数据
$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'];
$title = $data['title'];
$category = $data['category'];
$description = $data['description'];
$price = $data['price'];
$state = $data['state'];
$images = $data['images']; // 假设这是一个包含图片路径的数组
$colors = $data['colors']; // 颜色数组
$sizes = $data['sizes']; // 尺寸数组

// 将图片数组转换为字符串
$imagesString = implode(',', $images);

try {
    $pdo->beginTransaction();

    // 更新产品基本信息
    $sql = "UPDATE products SET title = :title, category = :category, description = :description, price = :price, state = :state, images = :images WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":state", $state);
    $stmt->bindParam(":images", $imagesString);
    $stmt->bindParam(":product_id", $product_id);
    $stmt->execute();

    // 清除旧的颜色信息
    $sql = "DELETE FROM product_color WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":product_id", $product_id);
    $stmt->execute();

    // 插入新的颜色信息
    foreach ($colors as $color) {
        $sql = "INSERT INTO product_color (product_id, color) VALUES (:product_id, :color)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':product_id' => $product_id, ':color' => $color]);
    }

    // 清除旧的尺寸信息
    $sql = "DELETE FROM product_size WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":product_id", $product_id);
    $stmt->execute();

    // 插入新的尺寸信息
    foreach ($sizes as $size) {
        $sql = "INSERT INTO product_size (product_id, size) VALUES (:product_id, :size)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':product_id' => $product_id, ':size' => $size]);
    }

    $pdo->commit();

    echo json_encode(["error" => false, "message" => "Product updated successfully"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["error" => true, "message" => "Failed to update product: " . $e->getMessage()]);
}
?>
