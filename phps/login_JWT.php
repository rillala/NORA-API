<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require_once '../vendor/firebase/php-jwt/src/BeforeValidException.php';
require_once '../vendor/firebase/php-jwt/src/ExpiredException.php';
require_once '../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
require_once '../vendor/firebase/php-jwt/src/JWT.php';



use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");

    // 前端以 POST 方法提交了 email 和 psw
    $email = $_POST['email'];
    $psw = $_POST['psw'];

    // 從資料庫檢索用戶信息
    $sql = "SELECT * FROM members WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($psw, $user['psw'])) {
        // 登入成功，生成 JWT token
        $key = $key = bin2hex(random_bytes(32)); // 這個密鑰應該儲存於安全的地方，並且保持不變
        
        $payload = [
            "iss" => "http://localhost", // 發行者
            "aud" => "http://localhost", // 觀眾
            "iat" => time(), // 簽發時間
            "exp" => time() + 7200, // 過期時間，這裡設定為2小時後
            "sub" => $user['member_id'], // 主題，通常是用戶ID
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        // 將 JWT token 儲存到資料庫
        $updateTokenSql = "UPDATE members SET token = ? WHERE member_id = ?";
        $updateStmt = $pdo->prepare($updateTokenSql);
        $updateStmt->execute([$jwt, $user['member_id']]);

        if ($updateStmt->rowCount() > 0) {
            // Token 更新成功
            echo json_encode(['error' => false, 'message' => '登入成功', 'token' => $jwt]);
        } else {
            // Token 更新失敗
            echo json_encode(['error' => true, 'message' => '登入成功，但 token 更新失敗']);
        }
    } else {
        // 密碼錯誤或用戶不存在
        echo json_encode(['error' => true, 'message' => '登入失敗：信箱或密碼錯誤!']);
    }
} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
} catch (Exception $e) {
    // 其他錯誤，比如 JWT 處理時出錯
    echo json_encode(['error' => true, 'message' => '服務器錯誤：' . $e->getMessage()]);
}
?>
