<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

try {
    // 準備 SQL 語句
    $sqlAll = "SELECT * FROM campsites ;";
    $sqlSet = "SELECT * FROM equipment WHERE title LIKE '%套組%' AND status = 1;";
    $sqlSingle = "SELECT * FROM equipment WHERE title NOT LIKE '%套組%' AND status = 1;";
    
    // 使用 PDO 將需求的資料取回
    $equipAll = $pdo -> query($sqlAll);
    $equipSet = $pdo -> query($sqlSet);
    $equipSingle = $pdo -> query($sqlSingle);  

    // 準備要回傳給前端的資料   
    $equipAllRows = $equipAll->fetchAll(PDO::FETCH_ASSOC);
    $equipSetRows = $equipSet->fetchAll(PDO::FETCH_ASSOC);
    $equipSingleRows = $equipSingle->fetchAll(PDO::FETCH_ASSOC);
    $result = ['error'=>false,"msg"=>"成功取得營位資料","all"=> $equipAllRows,"sets"=> $equipSetRows,"singles" => $equipSingleRows ];
    

} catch (PDOException $e) {
    //準備要回傳給前端的資料
     $result = ["error"=>true, "msg"=>$e->getMessage()];
}

//回傳資料給前端
echo json_encode($result);
?>