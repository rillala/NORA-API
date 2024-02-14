<?php
// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'connect_chd104g1.php'; // 包含您的資料庫連接設定

try {
    //使用 PDO
    $sql = "ALTER TABLE members MODIFY COLUMN psw VARCHAR(255)";
    $pdo->exec($sql);
    echo "欄位長度修改成功。";
} catch (Exception $e) {
    echo "欄位長度修改失敗: " . $e->getMessage();
}

?>
