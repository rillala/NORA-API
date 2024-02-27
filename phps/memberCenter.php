<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // 引入資料庫連接配置
    require_once 'connect_chd104g1.php';

    // 準備 SQL 查詢
    $sql = "SELECT * FROM members";
    $stmt = $pdo->prepare($sql);

    // 執行查詢
    $stmt->execute();

    // 獲取所有結果
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 將結果轉換為 JSON 格式並輸出
    echo json_encode(['error' => false, 'data' => $members]);

} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>
