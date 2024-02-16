<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

//連線到demo資料庫
require_once("./connect_chd104g1.php");

$reserveData = json_decode(file_get_contents("php://input"), true);

try {
     
    //準備sql指令    
    $sql = "INSERT INTO campsite_reservations (
        reservation_id,
        member_id,
        name,
        phone,
        email,
        address,
        checkin_date,
        checkout_date,
        has_discount,
        camp_price,
        equipment_price,
        total_price,
        reserve_status
    ) VALUES (
        :reservation_id,
        :member_id,
        :name,
        :phone,
        :email,
        :address,
        :checkin_date,
        :checkout_date,
        :has_discount,
        :camp_price,
        :equipment_price,
        :total_price,
        :reserve_status
    )";
    
    //編譯sql指令
    $reserve = $pdo->prepare($sql);

    //將資料放入並執行之
    $reserve->bindValue(":reservation_id", $reserveData["reservation_id"]);
    $reserve->bindValue(":member_id", $reserveData["member_id"]);
    $reserve->bindValue(":name", $reserveData["name"]);
    $reserve->bindValue(":phone", $reserveData["phone"]);
    $reserve->bindValue(":email", $reserveData["email"]);
    $reserve->bindValue(":address", $reserveData["address"]);
    $reserve->bindValue(":checkin_date", $reserveData["checkin_date"]);
    $reserve->bindValue(":checkout_date", $reserveData["checkout_date"]);
    $reserve->bindValue(":has_discount", $reserveData["has_discount"], PDO::PARAM_BOOL); // 注意布爾值的綁定
    $reserve->bindValue(":camp_price", $reserveData["camp_price"]);
    $reserve->bindValue(":equipment_price", $reserveData["equipment_price"]);
    $reserve->bindValue(":total_price", $reserveData["totalPrice"]);
    
    $reserve -> bindValue(":reserve_status", 1); // 預設"未入住"

    $reserve -> execute();
    //準備要回傳給前端的資料
    $result = ["error" => false, "msg"=>"成功新增一筆預約資料"];


    //準備要回傳給前端的資料
} catch (PDOException $e) {
    $result = ["error" => true, "msg"=>$e->getMessage()];
}


//回傳資料給前端
echo json_encode($result);
?>