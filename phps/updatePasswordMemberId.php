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


try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");
    
    // 解析從前端來的數據
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    // 使用 member_id 來驗證身份
    $member_id = $data->member_id;

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
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

// 清空並關閉輸出緩衝
// ob_end_flush();
?>
