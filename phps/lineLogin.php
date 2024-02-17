<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// 設定時區，以確保獲取正確的當前日期
date_default_timezone_set('Asia/Taipei');

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");

    // 獲取 POST 參數
    // $userId = $_POST['userId'];
    $nickname = $_POST['nickname'];
    $accountTypeID = $_POST['accountTypeID'];
    $photo = $_POST['photo'];
    // $date = date('Y-m-d'); // 獲取當前日期

    // 準備 SQL 語句
    $stmt = $pdo->prepare("INSERT INTO members ( name, email, photo) VALUES (:nickname, :accountTypeID, :photo)");

    // 綁定參數
    // $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':nickname', $name);
    // $stmt->bindParam(':accountTypeID', $accountTypeID);
    $stmt->bindParam(':photo', $photo);
    // $stmt->bindParam(':date', $date);

    // 執行 SQL 語句
    $stmt->execute();

    // 檢查是否成功
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => '用戶資料插入成功']);
    } else {
        echo json_encode(['error' => true, 'message' => '無法插入用戶資料']);
    }

} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>
