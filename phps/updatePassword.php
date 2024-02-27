<?php
// 啟動輸出緩衝
// ob_start();
error_log("Update password script called");
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
    
    // 解析從前端來的數據
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (!isset($data->oldPassword) || !isset($data->newPassword)) {
        throw new Exception("密碼資訊不完整");
    }
    
    // 解析 JWT
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $arr = explode(" ", $authHeader);
    if(count($arr) < 2) {
        throw new Exception("授權標頭格式錯誤");
    }
    $jwt = $arr[1];
    $key = "hi_this_is_nora_camping_project_for_CHD104g1"; // 你的密鑰
    $decoded = JWT::decode($jwt, new Key("hi_this_is_nora_camping_project_for_CHD104g1", 'HS256'));
    $member_id = $decoded->sub;

    // 驗證舊密碼
    $stmt = $pdo->prepare("SELECT psw FROM members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($data->oldPassword, $user['psw'])) {
        throw new Exception("舊密碼錯誤");
    }

    // 更新密碼
    $newPasswordHash = password_hash($data->newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE members SET psw = ? WHERE member_id = ?");
    $stmt->execute([$newPasswordHash, $member_id]);
    // 添加 success 字段到響應中
    echo json_encode(["success" => true, "message" => "密碼已成功更新，請重新登入"]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => $e->getMessage()]);
}

// 清空並關閉輸出緩衝
// ob_end_flush();
?>
