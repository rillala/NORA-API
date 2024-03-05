<?php
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header('Content-Type: application/json;charset=UTF-8');

    //連線到資料庫
    require_once("./connect_chd104g1.php");

    try{
    $sql = "SELECT * from news WHERE status = 1 order by publish_date desc";
    $news=$pdo->prepare($sql);
    $news->execute();
    
    if($news->rowCount()>0){
        $newsData=$news->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($newsData);
    }else{
        echo json_encode(["errMsg"=>""]);
    }
    
    }catch (PDOException $e) {
		echo "錯誤 : ", $e->getMessage(), "<br>";
	}
?>