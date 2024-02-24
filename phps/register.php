<?php
//ob_start();//啟動輸出緩衝
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");
try {
    // 引入資料庫連接配置


    // 前端以 POST 方法提交了 email ,psw和 name
    $name = $_POST['name'];
    $email = $_POST['email'];
    $psw = $_POST['psw'];

    // 檢查信箱、密碼和名字是否都已填寫
    if (empty($email) || empty($psw) || empty($name)) {
        throw new Exception('信箱、密碼和名字是必填欄位');
    }

    // 驗證郵箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('無效的郵箱格式');
    }

    // 檢查郵箱是否已被註冊
    $sql = "SELECT email FROM members WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('郵箱已被註冊');
    }

    // 設定時區，以確保獲取正確的當前日期和時間
    date_default_timezone_set('Asia/Taipei');
    // 獲取當前日期，格式為 YYYY-MM-DD
    $currentDate = date('Y-m-d');

    // 密碼加密
    $hashedPsw = password_hash($psw, PASSWORD_DEFAULT);

    // 插入資料庫
    $sql = "INSERT INTO members (email, psw, name, date) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$email, $hashedPsw, $name, $currentDate]);

    if (!$result) {
        throw new Exception('註冊失敗');
    }
    echo json_encode(['error' => false, 'message' => '註冊成功']);

} catch (Exception $e) {
    // 處理錯誤情況
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
?>
