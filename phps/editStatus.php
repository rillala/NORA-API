<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$editItem = json_decode(file_get_contents("php://input"), true);

function getIdByTableName($tableName) {
    $id = 0; // 預設 ID 值
    switch ($tableName) {
        case 'equipment':
            $id = "id";
            break;
        case 'campsites':
            $id = 'campsite_id';
            break;
        case 'admin':
            $id = "adminid";
            break;        
    }
    return $id;
}


try {
    $tableName = $editItem["tablename"];
    $id = getIdByTableName($tableName);
    //準備sql指令    
    $sql = "UPDATE  $tableName  SET status = :status WHERE $id = :id";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":status", $editItem["status"]);
    $stmt->bindValue(":id", $editItem["id"]);
   
    
    // 執行 SQL 指令
    $stmt->execute();
   

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功更新狀態"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>