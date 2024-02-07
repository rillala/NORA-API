<?php
 	//--------local
	$dbname = "nora";
	$user = "root";
	$password = "";
	$port = 3306;

	//------tibame
	// $dbname = "tibamefe_chd104g1";
	// $user = "tibamefe_since2021";
	// $password = "vwRBSb.j&K#E";
	// $port = 3306;

	$dsn = "mysql:host=localhost;port={$port};dbname=$dbname;charset=utf8";
	$options = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE=>PDO::CASE_NATURAL);
	//建立pdo物件
	$pdo = new PDO($dsn, $user, $password, $options);
?>