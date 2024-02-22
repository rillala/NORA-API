<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$editItem = json_decode(file_get_contents("php://input"), true);

function getIdByTableName($tableName) {
    $data = ["id" => "", "status" => ""]; // 使用陣列來同時返回ID和狀態的欄位名
    switch ($tableName) {
        case 'equipment':
            $data["id"] = "equipment_id"; // 假設設備表的ID欄位名為equipment_id
            $data["status"] = "status";
            break;
        case 'campsites':
            $data["id"] = 'campsite_id';
            $data["status"] = "status";
            break;
        // 其他情況...
    }
    return $data; // 返回包含id和status欄位名的陣列
}


try {
    $tableName = $editItem["tablename"];
    $data = getIdByTableName($tableName);
    $idField = $data["id"];
    $statusField = $data["status"];
    //準備sql指令    
    $sql = "UPDATE $tableName SET $statusField = :status WHERE $idField = :id";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到每一個項目
    $stmt->bindValue(":status", $editItem["status"]);
    $stmt->bindValue(":id", $editItem["id"]);
   
    
    // 執行 SQL 指令
    $stmt->execute();
   

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功更新上架狀態"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>