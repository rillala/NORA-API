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

require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 用戶已存在，進行更新操作
        $updateStmt = $pdo->prepare("UPDATE members SET name = :name, photo = :photo WHERE user_id = :user_id");
        $updateStmt->bindParam(':name', $name);
        $updateStmt->bindParam(':photo', $photo); 
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->execute();

        // 生成 JWT token
        $key = "hi_this_is_nora_camping_project_for_CHD104g1"; // 這個密鑰應該儲存於安全的地方，並且保持不變
        $payload = [
            "iss" => "http://localhost", // 發行者 打包更改處
            "aud" => "http://localhost", // 觀眾 　打包更改處
            "sub" => $user['member_id'], // 主題，通常是用戶ID
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        // 將 JWT token 儲存到資料庫
        $updateTokenSql = "UPDATE members SET token = ? WHERE member_id = ?";
        $updateTokenStmt = $pdo->prepare($updateTokenSql);
        $updateTokenStmt->execute([$jwt, $user['member_id']]);

        if ($updateTokenStmt->rowCount() > 0) {
            // Token 更新成功
            echo json_encode(['error' => false, 'message' => '登入成功', 'token' => $jwt]);
        } else {
            // Token 更新失敗
            echo json_encode(['error' => true, 'message' => '登入成功，但 token 更新失敗']);
        }
    } else {
        $unauthorizedEmail = "unauthorized_email"; // 定義一個常數或變數來儲存"unauthorized_email"這個值
        $uniquePasswordPlaceholder = "user_" . $user_id . "_placeholder_password"; // 保持密碼佔位符的產生方式不變

        $insertStmt = $pdo->prepare("INSERT INTO members (user_id, name, photo, date, email, psw) VALUES (:user_id, :name, :photo, :date, :email, :psw)");
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':photo', $photo);
        $insertStmt->bindParam(':date', $date);
        $insertStmt->bindParam(':email', $unauthorizedEmail); // 直接使用"unauthorized_email"這個固定值作為email的佔位符
        $insertStmt->bindParam(':psw', $uniquePasswordPlaceholder); // 使用產生的唯一密碼佔位符

        $insertStmt->execute();;

        // 獲取新插入的 member_id
        $newMemberId = $pdo->lastInsertId();

        // 生成 JWT token
        $key = "hi_this_is_nora_camping_project_for_CHD104g1"; // 這個密鑰應該儲存於安全的地方，並且保持不變
        $payload = [
            "iss" => "http://localhost", // 發行者 打包更改處
            "aud" => "http://localhost", // 觀眾 　打包更改處
            "sub" => $newMemberId, // 主題，通常是用戶ID
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        // 將 JWT token 儲存到資料庫
        $updateTokenSql = "UPDATE members SET token = ? WHERE member_id = ?";
        $updateTokenStmt = $pdo->prepare($updateTokenSql);
        $updateTokenStmt->execute([$jwt, $newMemberId]);

        if ($updateTokenStmt->rowCount() > 0) {
            // Token 更新成功
            echo json_encode(['error' => false, 'message' => 'line登入成功', 'token' => $jwt]);
        } else {
            // Token 更新失敗
            echo json_encode(['error' => true, 'message' => 'line登入成功，但 token 更新失敗']);
        }
    }
} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => '資料庫錯誤：' . $e->getMessage(),
        'errorInfo' => $pdo->errorInfo()
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => '服務器錯誤：' . $e->getMessage()]);
}
?>
