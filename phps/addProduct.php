<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
require_once("./connect_chd104g1.php");

// 檢查是否有文件被上傳
if (isset($_FILES['images'])) {
    $target_dir = "../image/shop/"; // 指定存儲上傳文件的目錄
    $target_file = $target_dir . basename($_FILES['images']['name']);

    // 可以在這裡添加更多的檢查，如檔案大小、格式等安全檢查

    // 移動上傳的文件到指定目錄
    if (move_uploaded_file($_FILES['images']['tmp_name'], $target_file)) {
        // 文件上傳成功
        $imagesPath = $target_file; // 存儲或處理文件路徑
    } else {
        // 文件上傳失敗，返回錯誤
        echo json_encode(["error" => true, "msg" => "圖片上傳失敗"]);
        exit;
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
$state = $_POST['state'];
$createdate = $_POST['createdate'];

try {
    // 準備 SQL 語句
    $sql = "INSERT INTO products (images, title, category, description, price, state, createdate) VALUES (:images, :title, :category, :description, :price, :state, :createdate)";
    $stmt = $pdo->prepare($sql);

    // 綁定參數
    $stmt->bindParam(":images", $imagesPath);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":state", $state);
    $stmt->bindParam(":createdate", $createdate);

    // 執行 SQL 語句
    $stmt->execute();

    // 返回成功訊息
    echo json_encode(["error" => false, "msg" => "新增成功"]);
} catch (PDOException $e) {
    // 返回錯誤訊息
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
