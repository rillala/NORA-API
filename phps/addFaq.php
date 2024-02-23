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
    $sql = "INSERT INTO faq_management (
        faq_status,
        faq_type,
        question,
        answer
    ) VALUES (
        :faq_status,
        :faq_type,
        :question,
        :answer
    )";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);


    // 綁定參數到每一個項目
    $stmt->bindValue(":faq_status", $Data["faq_status"]);
    $stmt->bindValue(":faq_type", $Data["faq_type"]);
    $stmt->bindValue(":question", $Data["question"]);
    $stmt->bindValue(":answer", $Data["answer"]);
    
    // 執行 SQL 指令
    $stmt->execute();
    

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"新增成功"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>