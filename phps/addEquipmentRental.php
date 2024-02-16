<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$equipmentData = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    $sql = "INSERT INTO equipment_rentals (
        reservation_id,
        equipment_id,
        rental_price,
        quantity,
        startDate,
        endDate
    ) VALUES (
        :reservation_id,
        :equipment_id,
        :rental_price,
        :quantity,
        :startDate,
        :endDate
    )";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);

    // 遍歷陣列，對每個裝備項目進行處理
    foreach ($equipmentData as $item) {
        // 綁定參數到每一個項目
        $stmt->bindValue(":reservation_id", $item["reservation_id"]);
        $stmt->bindValue(":equipment_id", $item["equipment_id"]);
        $stmt->bindValue(":rental_price", $item["rental_price"]);
        $stmt->bindValue(":quantity", $item["quantity"]);
        $stmt->bindValue(":startDate", $item["startDate"]);
        $stmt->bindValue(":endDate", $item["endDate"]);
        
        // 執行 SQL 指令
        $stmt->execute();
    }

    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功新增各別設備預約資料"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>