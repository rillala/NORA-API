<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

$data = json_decode(file_get_contents("php://input"), true);

try{
    //取得資料
    $adminid=$data["adminid"];
    $name = $data["name"];
    $acc = $data["acc"];
    $psw = $data["psw"];

    $checkSql="SELECT COUNT(*) FROM admin WHERE acc = :acc";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':acc' => $acc]);
    $count = $checkStmt->fetchColumn();

    if($count >0){
	$result=["error" => true, "msg"=>"帳號名稱已被使用"];
    } else {

     //sql指令
    $sql="UPDATE admin SET name= :name, acc= :acc, psw= :psw WHERE adminid= :adminid;";

    //編譯sql指令
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':adminid', $adminid);
    $stmt->bindValue(":name",$name);
    $stmt->bindValue(":acc",$acc);
    $stmt->bindValue(":psw",$psw);

    //執行sql指令
    $stmt->execute();

    $result=["error"=>false,"msg"=>"更新成功"];

}

   
} catch (PDOException $e) {
	//準備要回傳給前端的資料
	$result = ["error" => true, "msg" => $e->getMessage()];
}
//回傳資料給前端
echo json_encode($result);

?>