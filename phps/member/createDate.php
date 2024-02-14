<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");
    
    // 檢查 'date' 欄位是否存在
    $columnCheck = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'date'")->fetchAll();
    if (empty($columnCheck)) {
        // 欄位不存在，執行新增欄位的操作
        $pdo->exec("ALTER TABLE members ADD COLUMN date DATE");
        $pdo->exec($sql);
    }

    // 如果您希望給 'date' 欄位設定一個預設值，您可以在上述 SQL 語句中這樣做：
    // $sql = "ALTER TABLE members ADD COLUMN date DATE DEFAULT '2024-02-14'";

    // 插入日期資料到所有成員
    $dates = [
        '2023-01-01',
        '2023-03-02',
        '2023-05-01',
        '2023-09-01',
        '2023-09-01'
    ];

    // 使用迴圈插入每個日期到對應的成員，這裡假設 'member_id' 是從 1 開始遞增
    $sql = "UPDATE members SET date = ? WHERE member_id = ?";
    $stmt = $pdo->prepare($sql);
    
    foreach ($dates as $index => $date) {
        $stmt->execute([$date, $index + 1]); // member_id 假設為從 1 開始遞增
    }

    // 返回成功訊息給前端
    echo json_encode(['error' => false, 'message' => '日期欄位已新增，並且資料已成功更新']);

} catch (PDOException $e) {
    // 資料庫連接或查詢錯誤
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>
