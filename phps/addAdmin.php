<?php
header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

$addAdminData = json_decode(file_get_contents("php://input"), true);

try{
//連線到資料庫

$sql="INSERT INTO admin (name, acc, psw, status) VALUES  (:name, :acc, :psw, :status);";

$admin= $pdo->prepare($sql);

$admin->bindValue(":name",$addAdminData["name"]);
$admin->bindValue(":acc",$addAdminData["acc"]);
$admin->bindValue(":psw",$addAdminData["psw"]);
$admin->bindValue(":status",$addAdminData["status"]);

$admin->execute();

$result = ["error" => false, "msg" => "新增成功"];

} catch (PDOException $e) {
	//準備要回傳給前端的資料
	$result = ["error" => true, "msg" => $e->getMessage()];
}
//回傳資料給前端
echo json_encode($result);

?>