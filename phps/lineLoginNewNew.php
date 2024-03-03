<?php
// 將檢查使用者是否存在，如果存在，則更新使用者資訊和圖片（如果圖片 URL 有變化的話）。如果使用者不存在，則建立一個新使用者，並為新使用者產生一個 JWT Token。
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// 啟用錯誤報告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 設定時區
date_default_timezone_set('Asia/Taipei');

require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;

try {
    // 連接資料庫
    require_once("./connect_chd104g1.php");

    // 讀取 JSON 請求體
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    // 檢查 $input 是否為 null，或者必要的鍵值是否存在
    if (null === $input || !isset($input['user_id'], $input['name'], $input['photo'])) {
      echo json_encode(['error' => true, 'message' => '缺少必要的資料。']);
      exit;
    }

    // 使用 $input 中的值，去除了重複的代碼部分
    $user_id = $input['user_id'];
    $name = $input['name'];
    $lineUserImgURL = $input['photo'];
    $date = date('Y-m-d');

    // 檢查使用者是否已存在
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 使用者已存在，進行更新操作
        $filePath = downloadPhoto($lineUserImgURL);
        if ($filePath) {
            updateUserInfo($pdo, $user_id, $name, $filePath);
            $memberId = $user['member_id'];
        } else {
            echo json_encode(['error' => true, 'message' => '圖片下載失敗。']);
            return;
        }
    } else {
        // 使用者不存在，進行新使用者建立
        $filePath = downloadPhoto($lineUserImgURL);
        if ($filePath) {
            $memberId = createUser($pdo, $user_id, $name, $filePath, $date);
        } else {
            echo json_encode(['error' => true, 'message' => '圖片下載失敗。']);
            return;
        }
    }

    $jwt = JWT::encode($payload, $key, 'HS256');

    // 判斷條件語句（例如，檢查是否成功產生了token）
    // 由於已經確定不將 token 儲存於資料庫，故此部分代碼已註釋
        // 現在，直接回傳 JWT token 給客戶端
    echo json_encode(['error' => false, 'message' => 'line登入成功', 'token' => $jwt]);
    } else {
        echo json_encode(['error' => true, 'message' => 'line登入失敗']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
function downloadPhoto($photoUrl) {
  // 驗證圖片URL是否有效
  if (empty($photoUrl) || !filter_var($photoUrl, FILTER_VALIDATE_URL)) {
      error_log("Invalid or empty photo URL provided.");
      return false; // 返回false表示URL無效或空
  }

  // 初始化cURL會話
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $photoUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 將cURL獲取的內容以字符串返回，而不是直接輸出
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 允許跟隨重定向
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 對於https網站，如果SSL證書校驗失敗，設置為false可以繞過SSL檢查
  $data = curl_exec($ch);
  $error = curl_error($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // 檢查是否有錯誤或非200的HTTP響應代碼
  if ($error) {
      error_log("cURL error while downloading image: " . $error);
      return false; // 下載失敗
  }
  if ($httpcode != 200) {
      error_log("Download image failed, HTTP response code: " . $httpcode);
      return false;
  }

  // 指定圖片保存的目錄
  $uploadDir = '../image/member/'; // 更改為您的實際目錄
  if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
      error_log("Failed to create upload directory: " . $uploadDir);
      return false;
  }

  // 使用uniqid生成唯一文件名，防止文件名衝突
  $fileName = uniqid("img_", true) . '.jpg';
  $filePath = $uploadDir . $fileName;

  // 將圖片數據寫入文件
  if (!file_put_contents($filePath, $data)) {
      error_log("Failed to save the downloaded image to the file path: " . $filePath);
      return false; // 寫入失敗
  }

  return $filePath; // 返回文件路徑表示成功
}


function updateUserInfo($pdo, $userId, $name, $filePath) {
    $sql = "UPDATE members SET name = :name, photo = :photo WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':photo', $filePath);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
}

function createUser($pdo, $userId, $name, $filePath, $date) {
    // 定義SQL語句，用於插入新用戶的資訊
    $sql = "INSERT INTO members (user_id, name, photo, date, email, psw) VALUES (:user_id, :name, :photo, :date, :email, :psw)";
    
    // 定義一個常數或變數來儲存"unauthorized_email"這個值
    // $unauthorizedEmail = "unauthorized_email";
    
    // 保持密碼佔位符的產生方式不變
    // $uniquePasswordPlaceholder = "user_" . $userId . "_placeholder_password"; // 確保這裡使用的是$userId而不是$user_id
    
    // 預備SQL語句
    $stmt = $pdo->prepare($sql);
    
    // 綁定參數到預備語句
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':photo', $filePath);
    $stmt->bindParam(':date', $date);
    // $stmt->bindParam(':email', $unauthorizedEmail); // 修正變數名稱錯誤，應使用$stmt而非$insertStmt
    // $stmt->bindParam(':psw', $uniquePasswordPlaceholder); // 修正變數名稱錯誤，使用$stmt
    
    // 執行語句
    $stmt->execute();

    // 返回最後插入行的ID
    return $pdo->lastInsertId();
}

?>

