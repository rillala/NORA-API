<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

$data = json_decode(file_get_contents("php://input"), true);

try{

// 取得資料
$name = $data["name"];
$acc = $data["acc"];
$psw = $data["psw"];
$status = isset($data["status"]) ? $data["status"] : 1; // 如果 status 不存在，設置預設值為 1

// 在這裡執行你的 SQL 插入操作
// 假設 $pdo 是你的 PDO 連接
// 假設 "admin" 是資料表名稱

$checkSql="SELECT COUNT(*) FROM admin WHERE acc = :acc";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([':acc' => $acc]);
$count = $checkStmt->fetchColumn();

if($count >0){
	$result=["error" => true, "msg"=>"帳號名稱已被使用"];
} else {
	$sql = "INSERT INTO admin (name, acc, psw, status) VALUES (:name, :acc, :psw, :status)";
	
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(":name", $name);
	$stmt->bindValue(":acc", $acc);
	$stmt->bindValue(":psw", $psw);
	$stmt->bindValue(":status", $status);
	$stmt->execute();
	
	$result = ["error" => false, "msg" => "新增成功"];
}

} catch (PDOException $e) {
	//準備要回傳給前端的資料
	$result = ["error" => true, "msg" => "新增失敗：", "msg" => $e->getMessage()];
}
//回傳資料給前端
echo json_encode($result);

?>