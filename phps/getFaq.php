<?php
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header('Content-Type: application/json;charset=UTF-8');
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // 添加 OPTIONS 方法
    header("Access-Control-Allow-Headers: Content-Type, Authorization");


    require_once("./connect_chd104g1.php");
    
    try{
    $sql = "SELECT * from `faq_management`";

    $faq=$pdo->prepare($sql);
    $faq->execute();
    
    if($faq->rowCount()>0){
        $faqData=$faq->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($faqData);
    }else{
        echo json_encode(["errMsg"=>""]);
    }
    
    }catch (PDOException $e) {
		echo "錯誤 : ", $e->getMessage(), "<br>";
	}

    // ini_set("display_errors","On")
?>