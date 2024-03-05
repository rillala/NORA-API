<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$editData = json_decode(file_get_contents("php://input"), true);

try {

    //準備sql指令
    $sql = "UPDATE news SET title = :title, content = :content, img1 = :img1, img2 = :img2, img3 = :img3, publish_date = now() , status = :status  WHERE article_id = :article_id";

    // 編譯sql指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":title", $editData["title"]);
    $stmt->bindValue(":article_id", $editData["article_id"]);
    $stmt->bindValue(":content", $editData["content"]);
    $stmt->bindValue(":img1", $editData["img1"]);
    $stmt->bindValue(":img2", $editData["img2"]);
    $stmt->bindValue(":img3", $editData["img3"]);
    // $stmt->bindValue(":publish_date", $editData["publish_date"]);
    $stmt->bindValue(":status", $editData["status"]);

    // 執行sql指令
    $stmt->execute();

    // 準備要回傳給前端的數據
    $result = ["error" => false, "msg"=>"成功更新文章資料"];

} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}

// 回傳數據給前端
echo json_encode($result);
?>