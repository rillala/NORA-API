<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$Data = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    $sql = "INSERT INTO equipment (
        title,
        info,
        image,
        price,
        status
    ) VALUES (
        :title,
        :info,
        :image,
        :price,
        1
    )";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);


    // 綁定參數到每一個項目
    $stmt->bindValue(":title", $Data["title"]);
    $stmt->bindValue(":info", $Data["info"]);
    $stmt->bindValue(":image", $Data["image"]);
    $stmt->bindValue(":price", $Data["price"]);
       
    // 執行 SQL 指令
    $stmt->execute();
    

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功新增設備"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>