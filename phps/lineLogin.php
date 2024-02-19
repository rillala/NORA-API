<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 設定時區
date_default_timezone_set('Asia/Taipei');
// 獲取當前日期，格式為 YYYY-MM-DD
$currentDate = date('Y-m-d');

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");

    // 讀取 JSON 請求體
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true); // 將 JSON 轉換為 PHP 陣列

    // 從 JSON 請求中獲得的用戶資訊
    $user_id = $input['user_id']; // LINE 的用戶 ID
    $name = $input['name']; // 用戶的 LINE 暱稱
    $photo = $input['photo']; // 用戶的 LINE 頭像 URL
    $date = date('Y-m-d'); // 獲取當前日期和時間

    // 檢查用戶是否已存在
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $userExists = $stmt->fetchColumn() > 0;

    if ($userExists) {
        // 用戶已存在，進行更新操作
        $updateStmt = $pdo->prepare("UPDATE members SET name = :name, photo = :photo WHERE user_id = :user_id");
        $updateStmt->bindParam(':name', $name);
        $updateStmt->bindParam(':photo', $photo);
        $updateStmt->execute();

        echo json_encode(['success' => true, 'message' => '用戶資料已更新']);
    } else {
        // 用戶不存在，進行插入操作
        $insertStmt = $pdo->prepare("INSERT INTO members (user_id, name, photo, date) VALUES (:user_id, :name, :photo, :date)");
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':photo', $photo);
        $insertStmt->bindParam(':date', $date);
        $insertStmt->execute();

        echo json_encode(['success' => true, 'message' => '用戶資料已插入']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => '資料庫錯誤：' . $e->getMessage(),
        'errorInfo' => $pdo->errorInfo()
    ]);
}
