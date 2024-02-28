<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 如果是 OPTIONS 請求，直接返回成功
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 連接資料庫
require_once("./connect_chd104g1.php");

// 檢查是否收到 fileName 和 Authorization 頭部
if (isset($_POST['fileName']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $fileName = $_POST['fileName'];
    $token = null;
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    echo($_POST['fileName']);
    var_dump($_POST);
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }
    
    if (!$token) {
        echo json_encode(['success' => false, 'message' => 'Token not provided.']);
        exit;
    }
    
    try {
      // 使用實際的密鑰解碼 JWT token 以獲取用戶ID
      $decoded = JWT::decode($token, new Key("hi_this_is_nora_camping_project_for_CHD104g1", 'HS256'));
      $memberId = $decoded->sub; // 確保JWT的claim包含sub作為會員ID

      $filePath = 'member/' . $fileName; // 生成完整路徑

      // 更新資料庫
      $query = "UPDATE members SET photo = ? WHERE member_id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("si", $filePath, $memberId);

      if ($stmt->execute()) {
          echo json_encode(['success' => true, 'message' => '檔名更新成功']);
      } else {
          echo json_encode(['success' => false, 'message' => '資料庫更新失敗']);
      }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Token處理失敗: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => '缺少必要的參數']);
}
?>
