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

// 連接到資料庫
require_once("./connect_chd104g1.php");

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

// 發送驗證郵件
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // 請替換為你的 SMTP 伺服器地址
    $mail->SMTPAuth   = true;
    $mail->Username   = 'chd.noracamping@gmail.com'; // 請替換為你的郵箱地址
    $mail->Password   = 'Noranora104!'; // 請替換為你的郵箱密碼
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('chd.noracamping@gmail.com', 'Noracamping');
    $mail->addAddress($email, $name); // 將郵件發送到用戶註冊的郵箱地址

    //Content
    $mail->isHTML(true);
    $mail->Subject = '註冊確認信件';
    $mail->Body    = '請點擊以下連結以完成註冊：<a href="http://localhost:5173/registerAfterValid.php?email=' . $email . '&name=' . $name . '&psw=' . $psw . '">點此驗證</a>';

    $mail->send();
    echo json_encode(['error' => false, 'message' => '請到信箱點開連結驗證']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => '郵件發送失敗：' . $mail->ErrorInfo]);
}
?>
