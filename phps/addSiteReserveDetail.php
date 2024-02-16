<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$campsiteData = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    $sql = "INSERT INTO campsite_detail (
        reservation_id,
        checkin_date,
        checkout_date,
        type_id,
        reserve_count
    ) VALUES (
        :reservation_id,
        :checkin_date,
        :checkout_date,
        :type_id,
        :reserve_count
    )";
    
    // 編譯 SQL 指令
    $stmt = $pdo->prepare($sql);

    // 遍歷陣列，對每個裝備項目進行處理
    foreach ($campsiteData as $item) {
        // 綁定參數到每一個項目
        $stmt->bindValue(":reservation_id", $item["reservation_id"]);
        $stmt->bindValue(":checkin_date", $item["checkin_date"]);
        $stmt->bindValue(":checkout_date", $item["checkout_date"]);
        $stmt->bindValue(":type_id", $item["type_id"]);
        $stmt->bindValue(":reserve_count", $item["reserve_count"]);

        // 執行 SQL 指令
        $stmt->execute();
    }
    
    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功新增各別營位預約資料"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>