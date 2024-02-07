<?php
header("Access-Control-Allow-Origin:*");

//連線到demo資料庫
require_once("./connect_chd104g1.php");

try{
  
  // 準備好 sql 指令
  $sql = "SELECT * FROM campsites WHERE open_status = 1;";
  
  // 使用 PDO 將需求的資料取回
  $sites = $pdo -> query($sql);
  

  // 準備要回傳給前端的資料
if($sites ->rowCount() === 0){//查無部門員工資料
$result = ['error'=>false,"msg"=>"無營位資料","sites"=>"[]"];
}else{
  $siteRows = $sites->fetchAll(PDO::FETCH_ASSOC);
  $result = ['error'=>false,"msg"=>"成功取得營位資料","sites"=> $siteRows];
}

} catch (PDOException $e) {
  //準備要回傳給前端的資料
  $result = ["error"=>true, "msg"=>$e->getMessage()];
}

//回傳資料給前端
echo json_encode($result);
?>