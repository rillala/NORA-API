<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
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
        $key = "hi_this_is_nora_camping_project_for_CHD104g1";
        
        $payload = [
            // "iss" => "https://tibamef2e.com/chd104/g1/front",  //打包更改處
            // "aud" => "https://tibamef2e.com/chd104/g1/front",  //打包更改處
            "iss" => "http://localhost",
            "aud" => "http://localhost",
            "sub" => $user['member_id'],
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        // 由於已經確定不將 token 儲存於資料庫，故此部分代碼已註釋
        // 現在，直接回傳 JWT token 給客戶端
        echo json_encode(['error' => false, 'message' => '登入成功', 'token' => $jwt]);
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
