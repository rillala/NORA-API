<?php
//ob_start(); 
// 啟動輸出緩衝

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 設置 CORS 頭部
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 如果是 OPTIONS 請求，直接返回成功
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 引入 JWT 類
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");

    // 確保 $pdo 已被定義並連接到數據庫
    if (!isset($pdo) || $pdo === null) {
        throw new Exception('Database connection not established.');
    }

    // 從 HTTP Authorization Header 獲取 JWT token
    $token = null;
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    if (!$token) {
        throw new Exception('Token not provided.');
    }

    // 解碼 JWT token 以獲取用戶ID
    $decoded = JWT::decode($token, new Key("hi_this_is_nora_camping_project_for_CHD104g1", 'HS256'));
    $member_id = $decoded->sub;

    // 準備查詢
    $stmt = $pdo->prepare("SELECT member_id, name, phone, email, address, photo FROM members WHERE member_id = ?");
    $stmt->bindParam(1, $member_id, PDO::PARAM_INT);

    // 執行查詢
    $stmt->execute();

    // 檢查查詢結果
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 將會員資料轉換為 JSON 格式並輸出
        echo json_encode($row);
    } else {
        // 如果查詢失敗，返回錯誤訊息
        throw new Exception("Member not found.");
    }

    // 關閉資料庫連接
    $stmt = null;
    $pdo = null;

    // ob_end_flush();
} catch (Exception $e) {
    // 處理錯誤
    http_response_code(401);
    echo json_encode(array("error" => true, "message" => $e->getMessage()));
    ob_end_flush();
}
?>
