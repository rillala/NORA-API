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
    $sql = "UPDATE faq_management SET 
    faq_type = :faq_type, 
    question = :question, 
    answer = :answer,
    faq_status = :faq_status";



    // 加上條件
    $sql .= " WHERE faq_id = :faq_id";
    
    // 編譯sql指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":faq_id", $Data["faq_id"]);
    $stmt->bindValue(":faq_type", $Data["faq_type"]);
    $stmt->bindValue(":question", $Data["question"]);
    $stmt->bindValue(":answer", $Data["answer"]);
    $stmt->bindValue(":faq_status", $Data["faq_status"]);
    
    // 執行sql指令
    $stmt->execute();

    // 準備要回傳給前端的數據
    $result = ["error" => false, "msg"=>"更新成功"];

} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}

// 回傳數據給前端
echo json_encode($result);
?>