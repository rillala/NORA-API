<?php
// 啟動輸出緩衝
// ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置 CORS 頭部
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 如果是 OPTIONS 請求，直接返回成功
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once("./connect_chd104g1.php");

// 引入 JWT 類
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // 從 HTTP Authorization Header 獲取 JWT token
    $token = null;
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    if (!$token) {
        throw new Exception('Token not provided.');
    }

    // 使用實際的密鑰解碼 JWT token 以獲取用戶ID
    $decoded = JWT::decode($token, new Key("hi_this_is_nora_camping_project_for_CHD104g1", 'HS256'));
    $member_id = $decoded->sub;

    // 將資料庫中的 token 欄位清空
    $updateTokenSql = "UPDATE members SET token = NULL WHERE member_id = ?";
    $updateStmt = $pdo->prepare($updateTokenSql);
    $updateStmt->execute([$member_id]);

    if ($updateStmt->rowCount() > 0) {
        // Token 清除成功
        http_response_code(200);
        echo json_encode(['error' => false, 'message' => '登出成功'],JSON_UNESCAPED_UNICODE);
    } else {
        // Token 清除失敗
        http_response_code(400); 
        echo json_encode(['error' => true, 'message' => '登出失敗：未找到用戶或 token 已經清空'],JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
} catch (Exception $e) {
    // 其他錯誤，比如 JWT 處理時出錯
    http_response_code(401);
    echo json_encode(['error' => true, 'message' => '服務器錯誤：' . $e->getMessage()]);
}

// 結束輸出緩衝
// ob_end_flush();
?>
