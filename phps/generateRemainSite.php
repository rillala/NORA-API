<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

// 更新營位各種類可使用總數
$siteUpdateSql = "
UPDATE campsite_type ct
SET ct.count = (
    SELECT COUNT(*)
    FROM campsites c
    WHERE c.type_id = ct.type_id AND c.status = 1
);
";

// camp_reserve_statue:這邊會生成從現在日期算起往後三個月內的營位剩餘數量資料, 但不會刪除既有剩餘數量紀錄
$generateSql = " 
CREATE TEMPORARY TABLE IF NOT EXISTS temp_date_range (
    reserve_date DATE
  );
  
  INSERT INTO temp_date_range (reserve_date)
  SELECT reserve_date
  FROM (
    WITH RECURSIVE Date_Range AS (
      SELECT CURDATE() AS reserve_date
      UNION ALL
      SELECT reserve_date + INTERVAL 1 DAY
      FROM Date_Range
      WHERE reserve_date < CURDATE() + INTERVAL 3 MONTH
    ) SELECT reserve_date FROM Date_Range
  ) AS dates;
  
  INSERT INTO camp_reserve_statue (reserve_date, cat_grass, cat_shed, cat_pallet, dog_grass, dog_shed, dog_pallet)
  SELECT 
      d.reserve_date,
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 1 GROUP BY type.type_id) AS cat_grass,
  
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 2 GROUP BY type.type_id) AS cat_shed,
  
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 3 GROUP BY type.type_id) AS cat_pallet,
  
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 4 GROUP BY type.type_id) AS dog_grass,
  
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 5 GROUP BY type.type_id) AS dog_shed,
  
       (SELECT type.count - COALESCE(SUM(reserve.reserve_count), 0) FROM campsite_type AS type
      LEFT JOIN campsite_detail AS reserve ON type.type_id = reserve.type_id AND reserve.checkin_date <= d.reserve_date AND reserve.checkout_date > d.reserve_date WHERE 
      type.type_id = 6 GROUP BY type.type_id) AS dog_pallet
  
      
  FROM 
      temp_date_range AS d
  
  ON DUPLICATE KEY UPDATE 
      cat_grass = VALUES(cat_grass),
      cat_shed = VALUES(cat_shed),
      cat_pallet = VALUES(cat_pallet),
      dog_grass = VALUES(dog_grass),
      dog_shed = VALUES(dog_shed),
      dog_pallet = VALUES(dog_pallet);
  
  DROP TEMPORARY TABLE IF EXISTS temp_date_range;
  ";
  
  $getSql="SELECT 
  reserve_date AS `date`,
  cat_grass AS `1`, 
  cat_shed AS `2`,
  cat_pallet AS `3`, 
  dog_grass AS `4`, 
  dog_shed AS `5`,
  dog_pallet AS `6`
FROM camp_reserve_statue;";


  try {
    // campsite_type 總數更新    
    $stmt = $pdo->prepare($siteUpdateSql);
    $stmt->execute();
    
    // camp_reserve_statue 表格更新
    $stmt = $pdo->prepare($generateSql);
    $stmt->execute();
    $stmt->closeCursor();
    
    // 使用 PDO 將需求的資料取回
    $remains = $pdo -> query($getSql);

    // 準備要回傳給前端的資料   
    $remainRows = $remains->fetchAll(PDO::FETCH_ASSOC);
    
    $result = ['error'=>false,"msg"=>"成功更新並取得營位剩餘數量","all"=> $remainRows];    

} catch (PDOException $e) {
    //準備要回傳給前端的資料
     $result = ["error"=>true, "msg"=>$e->getMessage()];
}
  
echo json_encode($result);
?>


