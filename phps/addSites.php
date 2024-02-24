<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$siteData = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    $sql = "INSERT INTO campsites (type_id , status, info, price) VALUES (:type_id ,:status, :info, :price);";
    
    //編譯sql指令
    $site = $pdo->prepare($sql);

    //將資料放入並執行之
    $site -> bindValue(":type_id", $siteData["type_id"]);
    $site -> bindValue(":status", 1);
    $site -> bindValue(":price", $siteData["price"]);
    $site -> bindValue(":info", $siteData["info"]);
    $site -> execute();
    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功新增一筆營位資料"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>