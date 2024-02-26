<?php
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header('Content-Type: application/json;charset=UTF-8');

    //連線到資料庫
    require_once("./connect_chd104g1.php");

    try{
    $sql = "SELECT * from faq_management WHERE faq_status = 1";
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
?>