<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

$target_dir = "../image/shop/"; // 保留這行以指定存儲上傳文件的目錄

$imagesPaths = []; // 初始化一個陣列來存儲所有成功上傳的圖片路徑

// 檢查是否有文件被上傳
if (isset($_FILES['images'])) {
    // 處理每一個上傳的文件
    foreach ($_FILES['images']['name'] as $key => $name) {
        $target_file = $target_dir . basename($name);
        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
            // 文件上傳成功，將文件路徑添加到 $imagesPaths 陣列
            $imagesPaths[] = $target_file;
        } else {
            // 文件上傳失敗，返回錯誤
            echo json_encode(["error" => true, "msg" => "圖片上傳失敗"]);
            exit;
        }
    }
} else {
    // 沒有圖片被上傳，或處理上傳錯誤
    echo json_encode(["error" => true, "msg" => "沒有圖片被上傳"]);
    exit;
}

// 接收其他表單數據
$title = $_POST['title'];
$category = $_POST['category'];
$description = $_POST['description'];
$price = $_POST['price'];
$state = (int)$_POST['state'];
$createdate = $_POST['createdate'];
$colors = json_decode($_POST['colors']);
$sizes = json_decode($_POST['sizes']);

try {
    $pdo->beginTransaction(); // 開始事務

    // 將多個圖片路徑合併成一個字符串，例如透過逗號分隔
    $imagesPathString = join(',', $imagesPaths);

    // 準備 SQL 語句
    $sql = "INSERT INTO products (images, title, category, description, price, state, createdate) VALUES (:images, :title, :category, :description, :price, :state, :createdate)";
    $stmt = $pdo->prepare($sql);

    // 綁定參數，包括圖片路徑字符串
    $stmt->bindParam(":images", $imagesPathString);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":state", $state);
    $stmt->bindParam(":createdate", $createdate);

    // 執行 SQL 語句
    $stmt->execute();

    // 獲取剛插入商品的ID
    $product_id = $pdo->lastInsertId(); 

    // 插入顏色信息
    foreach ($colors as $color) {
        $sql_color = "INSERT INTO product_color (product_id, color) VALUES (:product_id, :color)";
        $stmt_color = $pdo->prepare($sql_color);
        $stmt_color->execute(['product_id' => $product_id, 'color' => $color]);
    }

    // 插入尺寸信息
    foreach ($sizes as $size) {
        $sql_size = "INSERT INTO product_size (product_id, size) VALUES (:product_id, :size)";
        $stmt_size = $pdo->prepare($sql_size);
        $stmt_size->execute(['product_id' => $product_id, 'size' => $size]);
    }

    $pdo->commit(); // 提交事務

    // 返回成功訊息
    echo json_encode(["error" => false, "msg" => "新增成功"]);
} catch (PDOException $e) {
    // 返回錯誤訊息
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
