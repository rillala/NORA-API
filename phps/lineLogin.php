<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 設定時區
date_default_timezone_set('Asia/Taipei');
// 獲取當前日期，格式為 YYYY-MM-DD
$currentDate = date('Y-m-d');

// 包含連接資料庫的程式碼
require_once "./connect_chd104g1.php";

// 讀取 JSON 請求體
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true); // 將 JSON 轉換為 PHP 陣列

// 從 JSON 請求中獲得的用戶資訊
$user_id = $input['user_id']; // LINE 的用戶 ID
$name = $input['name']; // 用戶的 LINE 暱稱
$date = date('Y-m-d'); // 獲取當前日期和時間

// 上傳文件部分
$uploadDir = '../image/member/'; // 指定存儲上傳文件的目錄
$fileName = $_FILES['file']['name']; // 獲取上傳的檔名
$uploadFile = $uploadDir . $fileName; // 創建存儲路徑

// 將上傳的文件移動到指定目錄
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    $filePath = 'member/' . $fileName; // 生成完整路徑

    // 檢查用戶是否已存在
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 用戶已存在，進行更新操作
        $updateStmt = $pdo->prepare("UPDATE members SET name = :name, photo = :photo WHERE user_id = :user_id");
        $updateStmt->bindParam(':name', $name);
        $updateStmt->bindParam(':photo', $filePath); 
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->execute();

        echo json_encode(['error' => false, 'message' => '用戶更新成功']);
    } else {
        // 用戶不存在，進行插入操作
        $insertStmt = $pdo->prepare("INSERT INTO members (user_id, name, photo, date) VALUES (:user_id, :name, :photo, :date)");
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':photo', $filePath);
        $insertStmt->bindParam(':date', $date);
        $insertStmt->execute();

        echo json_encode(['error' => false, 'message' => '用戶插入成功']);
    }
} else {
    // 如果移動文件失敗，則返回錯誤訊息
    echo json_encode(['error' => true, 'message' => '檔案上傳失敗。']);
}
?>
