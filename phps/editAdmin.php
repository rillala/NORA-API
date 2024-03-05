<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

$data = json_decode(file_get_contents("php://input"), true);

try{
    //取得資料
    $adminid = $data["adminid"];
    $name = $data["name"];
    $acc = $data["acc"];
    $psw = $data["psw"];
    
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
    
    $result = ["error" => false, "msg" => "更新成功"];

   } catch (PDOException $e) {
	
    $errorInfo = $stmt->errorInfo();
    if ($errorInfo[0] === "23000" && $errorInfo[1] === 1062) {
        // 唯一鍵違反錯誤，SQLSTATE 為 "23000"，錯誤代碼為 1062
        $result = ["error" => true, "msg" => "帳號名稱重複"];
    } else {
        // 其他錯誤
        $result = ["error" => true, "msg" => "更新失敗：" . $e->getMessage()];
}}
//回傳資料給前端
echo json_encode($result);

?>