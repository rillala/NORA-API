<?php
// Adding headers for CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json;charset=UTF-8');

// Enabling error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Setting the timezone
date_default_timezone_set('Asia/Taipei');

require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;

try {
    // Connecting to the database
    require_once("./connect_chd104g1.php");

    // Reading the JSON request body
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    // Checking if $input is null or if necessary keys are missing
    if (null === $input || !isset($input['user_id'], $input['name'], $input['photo'])) {
      echo json_encode(['error' => true, 'message' => 'Missing required data.']);
      exit;
    }

    // Using values from $input
    $user_id = $input['user_id'];
    $name = $input['name'];
    $lineUserImgURL = $input['photo'];
    $date = date('Y-m-d');

    // Checking if the user already exists
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Updating or creating user based on existence
    if ($user) {
        // Updating user information if user exists
        $filePath = downloadPhoto($lineUserImgURL);
        if ($filePath) {
            updateUserInfo($pdo, $user_id, $name, $filePath);
            $memberId = $user['member_id'];
        } else {
            echo json_encode(['error' => true, 'message' => 'Failed to download photo.']);
            return;
        }
    } else {
        // Creating new user if user doesn't exist
        $filePath = downloadPhoto($lineUserImgURL);
        if ($filePath) {
            $memberId = createUser($pdo, $user_id, $name, $filePath, $date);
        } else {
            echo json_encode(['error' => true, 'message' => 'Failed to download photo.']);
            return;
        }
    }

    // Generating JWT Token
    $key = "hi_this_is_nora_camping_project_for_CHD104g1";
    $payload = [
        "iss" => "http://localhost", // Issuer
        "aud" => "http://localhost", // Audience
        "sub" => $memberId, // Subject
    ];
    $jwt = JWT::encode($payload, $key, 'HS256');

    // Returning the token to the frontend
    echo json_encode(['error' => false, 'message' => 'Login successful', 'token' => $jwt]);
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Server error: ' . $e->getMessage()]);
}

function downloadPhoto($photoUrl) {
  // Validating photo URL
  if (empty($photoUrl) || !filter_var($photoUrl, FILTER_VALIDATE_URL)) {
      error_log("Invalid or empty photo URL provided.");
      return false;
  }

  // Initializing cURL session
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $photoUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $data = curl_exec($ch);
  $error = curl_error($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Checking for errors or non-200 HTTP response code
  if ($error) {
      error_log("cURL error while downloading image: " . $error);
      return false;
  }
  if ($httpcode != 200) {
      error_log("Download image failed, HTTP response code: " . $httpcode);
      return false;
  }

  // Specifying the directory to save the image
  $uploadDir = '../image/member/';
  if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
      error_log("Failed to create upload directory: " . $uploadDir);
      return false;
  }

  // Generating a unique filename to avoid conflicts
  $fileName = uniqid("img_", true) . '.jpg';
  $filePath = $uploadDir . $fileName;

  // Writing image data to file
  if (!file_put_contents($filePath, $data)) {
      error_log("Failed to save the downloaded image to the file path: " . $filePath);
      return false;
  }

  return $filePath;
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
