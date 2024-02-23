<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

try {
   // 查詢所有訂單信息
   $reserveSql = "SELECT * FROM campsite_reservations;";
   $stmtReserve = $pdo->query($reserveSql);
   $reserves = $stmtReserve->fetchAll(PDO::FETCH_ASSOC);

   // 查詢所有房間明細
   $siteDetailSql = "SELECT reservation_id, type_id, reserve_count FROM campsite_detail;";
   $stmtSiteDetail = $pdo->query($siteDetailSql);
   $siteDetails = $stmtSiteDetail->fetchAll(PDO::FETCH_ASSOC);

   // 查詢所有設備明細
   $equipmentDetailsSql = "
   SELECT er.reservation_id, er.equipment_id, er.quantity, eo.title
   FROM equipment_rentals AS er
   JOIN equipment AS eo ON er.equipment_id = eo.id;";
   $stmtEquipmentDetails = $pdo->query($equipmentDetailsSql);
   $equipmentDetails = $stmtEquipmentDetails->fetchAll(PDO::FETCH_ASSOC);

   // 組織數據
   $results = [];
   foreach ($reserves as $order) {
       $siteDetailList = array_filter($siteDetails, function ($detail) use ($order) {
           return $detail['reservation_id'] == $order['reservation_id'];
       });

       $equipmentDetailList = array_filter($equipmentDetails, function ($detail) use ($order) {
           return $detail['reservation_id'] == $order['reservation_id'];
       });

       $results[] = [
           'orderInfo' => $order,
           'siteInfo' => array_values($siteDetailList), // 重置陣列索引
           'rentInfo' => array_values($equipmentDetailList), // 重置陣列索引
       ];
   }
   $result = ['error'=>false,"msg"=>"成功取得預約訂單資料","all"=> $results ];

} catch (PDOException $e) {
    //準備要回傳給前端的資料
     $result = ["error"=>true, "msg"=>$e->getMessage()];
}

//回傳資料給前端
echo json_encode($result);
?>