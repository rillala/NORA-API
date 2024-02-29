<?php
// 將檢查使用者是否存在，如果存在，則更新使用者資訊和圖片（如果圖片 URL 有變化的話）。如果使用者不存在，則建立一個新使用者，併為新使用者產生一個 JWT Token。
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

    // 產生JWT Token
    $key = "hi_this_is_nora_camping_project_for_CHD104g1";
    $payload = [
        "iss" => "http://localhost",
        "aud" => "http://localhost",
        "sub" => $user['member_id'],
        // "iat" => time(),
        // "exp" => time() + (60*60*24) // Token有效期一天
    ];
    $jwt = JWT::encode($payload, $key, 'HS256');

    // 將 JWT token 儲存到資料庫
    $updateTokenSql = "UPDATE members SET token = ? WHERE member_id = ?";
    $updateTokenStmt = $pdo->prepare($updateTokenSql);
    $updateTokenStmt->execute([$jwt, $user['member_id']]);

    if ($updateTokenStmt->rowCount() > 0) {
        // Token 更新成功
        echo json_encode(['error' => false, 'message' => '登入成功', 'token' => $jwt]);
    } else {
        // Token 更新失敗
        echo json_encode(['error' => true, 'message' => '登入成功，但 token 更新失敗']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'message' => '資料庫錯誤：' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => '伺服器錯誤：' . $e->getMessage()]);
}

function downloadPhoto($photoUrl, $existingPhotoPath = null) {
    // 如果URL相同，則不重新下載圖片
    if ($existingPhotoPath && basename($existingPhotoPath) == basename(parse_url($photoUrl, PHP_URL_PATH))) {
        return $existingPhotoPath;
    }

    $fileName = basename(parse_url($photoUrl, PHP_URL_PATH));
    $uploadDir = '../image/member/';
    $uploadFile = $uploadDir . $fileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (file_put_contents($uploadFile, file_get_contents($photoUrl))) {
        return 'image/member/' . $fileName;
    } else {
        return false;
    }
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
    $sql = "INSERT INTO members (user_id, name, photo, date) VALUES (:user_id, :name, :photo, :date)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':photo', $filePath);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    return $pdo->lastInsertId();
}
?>
