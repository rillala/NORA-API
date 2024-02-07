<?php
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header('Content-Type: application/json;charset=UTF-8');
    
    $dbname = "nora";
    $user = "root";
    $password = "";
    $port = 3306;

    $dsn = "mysql:host=localhost;port={$port};dbname=$dbname;charset=utf8";
    $options = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE=>PDO::CASE_NATURAL);
    //建立pdo物件
    
    $pdo = new PDO($dsn, $user, $password, $options);
    //要用require_once連結到另一個php檔案的connect內容

    try{
    $sql = "select * from admin";
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
?>