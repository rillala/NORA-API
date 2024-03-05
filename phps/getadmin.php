<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json;charset=UTF-8');
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

//連線到資料庫
require_once("./connect_chd104g1.php");

try{  
    
    $sql = "SELECT * FROM admin";
    
    $admin=$pdo->prepare($sql);
    $admin->execute();
    
    if($admin->rowCount()>0){
        $adminData=$admin->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["all" => $adminData]);
    }else{
        echo json_encode(["errMsg"=>""]);
    }
    
}catch (PDOException $e) {
    echo "錯誤 : ", $e->getMessage(), "<br>";
}

//顯示編碼錯誤
ini_set("display_errors","On")
?>