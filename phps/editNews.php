<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$editArticle = json_decode(file_get_contents("php://input"), true);

try {

    //準備sql指令
    $sql = "UPDATE news SET title = :title, content = :content, img1 = :img1, img2 = :img2, img3 = :img3, status = :status";

    // 判斷是否需要更新圖片
    if ($editArticle["img1"] !== "") {
        // 根據前端發送的數據中的img1、img2、img3的值來判斷是否更新圖片
        $sql .= ", img1 = :img1";
    }
    if ($editArticle["img2"] !== "") {
        $sql .= ", img2 = :img2";
    }
    if ($editArticle["img3"] !== "") {
        $sql .= ", img3 = :img3";
    }

    // 加上條件
    $sql .= " WHERE id = :id";
    
    // 編譯sql指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":title", $editArticle["title"]);
    $stmt->bindValue(":content", $editArticle["content"]);
    $stmt->bindValue(":img1", $editArticle["img1"]);
    $stmt->bindValue(":img2", $editArticle["img2"]);
    $stmt->bindValue(":img3", $editArticle["img3"]);
    $stmt->bindValue(":status", $editArticle["status"]);

    // 只有當對應的欄位值不為空時才綁定參數
    if ($editArticle["img1"] !== "") {
        $stmt->bindValue(":img1", $editArticle["img1"]);
    }
    if ($editArticle["img2"] !== "") {
        $stmt->bindValue(":img2", $editArticle["img2"]);
    }
    if ($editArticle["img3"] !== "") {
        $stmt->bindValue(":img3", $editArticle["img3"]);
    }
    
    // 執行sql指令
    $stmt->execute();

    // 準備要回傳給前端的數據
    $result = ["error" => false, "msg"=>"成功更新文章资料"];

} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}

// 回傳數據給前端
echo json_encode($result);
?>