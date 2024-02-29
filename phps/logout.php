<?php
// 啟動輸出緩衝
// ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置 CORS 頭部
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");
$Data = json_decode(file_get_contents("php://input"), true);

try {   
    // 將資料庫中的 token 欄位清空
    $updateTokenSql = "UPDATE members SET token = NULL WHERE member_id = :member";
    $updateStmt = $pdo->prepare($updateTokenSql);
    $updateStmt->bindValue(":member", $Data["data"]);   
    $updateStmt->execute();

    //準備要回傳給前端的資料
    $result = ["error" => false, "message"=>"成功清空token"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "message"=>$e->getMessage()];
}

//回傳資料給前端
echo json_encode($result);
?>
