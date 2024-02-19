<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$equipData = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    if ($equipData["image"] === "") {
        // 如果 image 為空字串，則不更新 image 欄位
        $sql = "UPDATE equipment SET title = :title, info = :info, price = :price WHERE id = :id";
    } else {
        // 如果 image 不為空，則包含 image 欄位在更新指令中
        $sql = "UPDATE equipment SET title = :title, info = :info, image = :image, price = :price WHERE id = :id";
    }
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":title", $equipData["title"]);
    $stmt->bindValue(":info", $equipData["info"]);
    $stmt->bindValue(":price", $equipData["price"]);
    $stmt->bindValue(":id", $equipData["id"]);
    if ($equipData["image"] !== "") {
        // 只有當 image 不為空時才綁定 image 參數
        $stmt->bindValue(":image", $equipData["image"]);
    }
    
    // 執行 SQL 指令
    $stmt->execute();
   

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功更新設備詳細資料"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>