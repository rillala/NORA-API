<?php
header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json;charset=UTF-8');

//連線到資料庫
require_once("./connect_chd104g1.php");

try{
    // $dbname = "nora";
    // $user = "root";
    // $password = "";
    // $port = 3306;
    
    // $dsn = "mysql:host=localhost;port={$port};dbname=$dbname;charset=utf8";
    // $options = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE=>PDO::CASE_NATURAL);
    // //建立pdo物件
    
    // $pdo = new PDO($dsn, $user, $password, $options);
    
    $sql = "SELECT * FROM admin";
    
    $admin=$pdo->prepare($sql);
    $admin->execute();
    
    if($admin->rowCount()>0){
        $adminData=$admin->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($adminData);
    }else{
        echo json_encode(["errMsg"=>""]);
    }
    
}catch (PDOException $e) {
    echo "錯誤 : ", $e->getMessage(), "<br>";
}

//顯示編碼錯誤
ini_set("display_errors","On")
?>