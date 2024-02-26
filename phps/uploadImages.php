<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

$target_dir = "../image/shop/";
$imagesPaths = [];
$product_id = $_POST['product_id']; // 假设前端在 FormData 中添加了 product_id

if (isset($_FILES['images'])) {
    $pdo->beginTransaction(); // 开始事务，以保证数据的一致性
    foreach ($_FILES['images']['name'] as $key => $name) {
        // 生成目标文件的完整路径
        $target_file = $target_dir . basename($_FILES['images']['name'][$key]);
        // 生成相对于网站根目录的路径，用于保存到数据库
        $relativePath = "../image/shop/" . basename($_FILES['images']['name'][$key]);

        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
            $imagesPaths[] = $relativePath; // 使用相对路径

            // 上传成功后，将图片路径保存到数据库
            $sql = "UPDATE products SET images = CONCAT(images, ',', :imagePath) WHERE product_id = :productId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':imagePath', $relativePath);
            $stmt->bindParam(':productId', $product_id);
            $stmt->execute();
        } else {
            // 如果任何文件上传失败，则回滚事务，并退出脚本
            $pdo->rollBack();
            echo json_encode(["error" => true, "msg" => "图片上传失败"]);
            exit;
        }
    }
    $pdo->commit(); // 提交事务
    echo json_encode(["error" => false, "imagesPaths" => $imagesPaths]);
} else {
    echo json_encode(["error" => true, "msg" => "没有图片被上传"]);
}
?>
