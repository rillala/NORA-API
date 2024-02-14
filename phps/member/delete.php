<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");
    


} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>
