<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

require_once("./connect_chd104g1.php");

if (isset($_FILES['file'])) {
  $file = $_FILES['file']; // 獲取上傳的文件

  //move_uploaded_file用於將上傳的文件從臨時目錄移動到指定的目

  if ($file['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../image/member/'; // 指定存儲上傳文件的目錄
    $uploadFile = $uploadDir . basename($file['name']); // 創建存儲路徑
    //basename() 是一個 PHP 函式，它返回給定文件路徑的基本名稱。換句話說，它會從完整的文件路徑中擷取文件名，包括副檔名

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        echo htmlspecialchars(basename($file['name']));
        //echo ($file['name']);
        //htmlspecialchars() 函數會將特殊字符轉換為HTML實體。這是一個安全措施，用來防止跨站腳本攻擊（XSS）。如果文件名包含像 <, >, &, " 這樣的特殊字符，它們將被轉換成相應的HTML實體
    } else {
        echo "新檔案上傳失敗。";
    }
  } else {
      echo "檔案上傳過程中出現錯誤。";
  }
} else {
  echo "未接收到檔案。";
}
?>
