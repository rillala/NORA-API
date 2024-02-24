<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

try{

  // 準備好 sql 指令
  $sql = "SELECT * FROM members;";
  
  // 使用 PDO 將需求的資料取回
  $members = $pdo -> query($sql);
  

  // 準備要回傳給前端的資料
if($members ->rowCount() === 0){//查無部門員工資料
$result = ['error'=>false,"msg"=>"無會員資料","members"=>"[]"];
}else{
  $memberRows = $members->fetchAll(PDO::FETCH_ASSOC);
  $result = ['error'=>false,"msg"=>"成功取得會員資料","members"=> $memberRows];
}

} catch (PDOException $e) {
  //準備要回傳給前端的資料
  $result = ["error"=>true, "msg"=>$e->getMessage()];
}
//回傳資料給前端
echo json_encode($result);
?>