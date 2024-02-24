<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");
    
    // 獲取JSON格式的PUT請求體
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // 準備SQL更新語句
    $sql = "UPDATE members SET name = :name, phone = :phone, address = :address WHERE member_id = :member_id";

    // 預處理SQL語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
    $stmt->bindParam(':member_id', $data['member_id'], PDO::PARAM_INT);

    // 執行SQL語句
    $stmt->execute();

    // 檢查是否有行被更新
    if ($stmt->rowCount()) {
        echo json_encode(array("message" => "會員資料更新成功。"));
    } else {
        echo json_encode(array("message" => "沒有資料被更新，請檢查會員ID是否正確。"));
    }
} catch(PDOException $e) {
    // 捕捉到PDO異常
    echo json_encode(array("message" => "資料庫錯誤：" . $e->getMessage()));
} catch(Exception $e) {
    // 捕捉到一般異常
    echo json_encode(array("message" => "發生錯誤：" . $e->getMessage()));
}
?>
