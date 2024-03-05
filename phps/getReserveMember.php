<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("./connect_chd104g1.php");

$member = json_decode(file_get_contents("php://input"), true);
$memberId = $member["memberId"];

try {
   // 查詢所有訂單信息
   $reserveSql = "SELECT * FROM campsite_reservations WHERE member_id = :memberId;";
   $stmtReserve = $pdo->prepare($reserveSql);
   $stmtReserve->execute([':memberId' => $memberId]);
   $reserves = $stmtReserve->fetchAll(PDO::FETCH_ASSOC);

   // 查詢所有房間明細
   $siteDetailSql = "SELECT cd.reservation_id, cd.type_id, cd.reserve_count 
   FROM campsite_detail AS cd 
   WHERE cd.reservation_id IN (
       SELECT cr.reservation_id 
       FROM campsite_reservations AS cr 
       WHERE cr.member_id = :memberId
   )";
   $stmtSiteDetail = $pdo->prepare($siteDetailSql);
   $stmtSiteDetail->execute([':memberId' => $memberId]);
   $siteDetails = $stmtSiteDetail->fetchAll(PDO::FETCH_ASSOC);
   

   // 查詢所有設備明細
   $equipmentDetailsSql = "
SELECT er.reservation_id, er.equipment_id, er.quantity, eo.title
FROM equipment_rentals AS er
JOIN equipment AS eo ON er.equipment_id = eo.id
WHERE er.reservation_id IN (
    SELECT cr.reservation_id 
    FROM campsite_reservations AS cr 
    WHERE cr.member_id = :memberId
)";
$stmtEquipmentDetails = $pdo->prepare($equipmentDetailsSql);
$stmtEquipmentDetails->execute([':memberId' => $memberId]);
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