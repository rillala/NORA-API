<?php
//ob_start();//啟動輸出緩衝
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

// 引入必要的JWT類
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // 從 HTTP Authorization Header 獲取 JWT token
    $token = null;
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($authHeader, 'Bearer') === 0) {
        $token = substr($authHeader, 7);
    }

    if (!$token) {
        throw new Exception('Token not provided.');
    }

    // 解碼 JWT token 以獲取用戶ID
    $decoded = JWT::decode($token, new Key("your_long_term_secret_key", 'HS256'));
    $member_id = $decoded->sub;

    // 將資料庫中的 token 欄位清空
    $updateTokenSql = "UPDATE members SET token = NULL WHERE member_id = ?";
    $updateStmt = $pdo->prepare($updateTokenSql);
    $updateStmt->execute([$member_id]);

    if ($updateStmt->rowCount() > 0) {
        // Token 清除成功
        echo json_encode(['error' => false, 'message' => '登出成功']);
    } else {
        // Token 清除失敗
        echo json_encode(['error' => true, 'message' => '登出失敗：未找到用戶或token已經清空']);
    }
} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
} catch (Exception $e) {
    // 其他錯誤，比如JWT處理時出錯
    http_response_code(401);
    echo json_encode(['error' => true, 'message' => '服務器錯誤：' . $e->getMessage()]);
}

//ob_end_flush()
?>
