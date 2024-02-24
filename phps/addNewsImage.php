<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
file_put_contents("debug.txt", print_r($_POST, true));

require_once("./connect_chd104g1.php");

// 刪除原本的圖片
if (isset($_POST['oldImgPath'])) {
    $oldImagePath = $_POST['oldImgPath'];

    // 檢查舊圖片是否存在
    if (file_exists($oldImagePath)) {
        // 嘗試刪除舊圖片
        if (unlink($oldImagePath)) {
            echo "舊圖片已成功刪除。\n";
        } else {
            echo "舊圖片刪除失敗。\n";
        }
    } else {
        echo "can't find old pic";
    }
}

// 處理新圖片的上傳
if (isset($_FILES['file'])) {
    $file = $_FILES['file']; // 獲取上傳的文件

    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../image/news/'; // 指定儲存上傳文件的目錄
        $uploadFile = $uploadDir . basename($file['name']); // 創建儲存路徑

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            echo "新檔案 " . htmlspecialchars(basename($file['name'])) . " 已成功上傳。";
        } else {
            echo "新檔案上傳失敗。";
        }
    } else {
        echo "檔案上傳過程中出現錯誤。";
    }
} else {
    echo "未接收到檔案。";
}

?>
