<?php
//ob_start();//啟動輸出緩衝
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require 'vendor/autoload.php'; // 請確保你已經安裝了相應的郵件庫
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once("./connect_chd104g1.php");

try {
    // 確保 GET 請求中包含必要的資訊
    if (!isset($_GET['email']) || !isset($_GET['name']) || !isset($_GET['psw'])) {
        throw new Exception('缺少必要的註冊資訊');
    }

    // 從 GET 請求中獲取用戶的註冊資訊
    $email = $_GET['email'];
    $name = $_GET['name'];
    $psw = $_GET['psw'];

    // 設定時區
    date_default_timezone_set('Asia/Taipei');
    // 獲取當前日期，格式為 YYYY-MM-DD
    $currentDate = date('Y-m-d');

    // 將用戶資料寫入到會員資料表中
    $sql = "INSERT INTO members (email, psw, name, date) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    // 將密碼進行哈希
    $hashedPsw = password_hash($psw, PASSWORD_DEFAULT);
    $result = $stmt->execute([$email, $hashedPsw, $name, $currentDate]);

    if (!$result) {
        throw new Exception('寫入資料庫失敗');
    }

    // 返回成功的 JSON 響應
    echo json_encode(['error' => false, 'message' => '註冊成功']);

} catch (Exception $e) {
    // 處理錯誤情況並返回錯誤的 JSON 響應
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
?>
