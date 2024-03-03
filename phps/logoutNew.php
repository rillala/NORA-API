<?php
// 啟動輸出緩衝
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置 CORS 頭部
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 無需連接資料庫或修改任何資料庫記錄
// 登出操作完全在客戶端處理

// 回傳成功登出的訊息
echo json_encode(["error" => false, "message" => "成功登出"]);
?>
