<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

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

    // 檢查是否找到了用戶並驗證哈希密碼
    if ($user && password_verify($psw, $user['psw'])) {
        // 登入成功，生成 token 或進行其他必要的處理
        echo json_encode(['error' => false, 'message' => '登入成功']);
    } else {
        // 密碼錯誤或用戶不存在
        echo json_encode(['error' => true, 'message' => '登入失敗：信箱或密碼錯誤!']);
    }
} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>
