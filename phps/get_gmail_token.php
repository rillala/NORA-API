<?php

// 定義用戶端ID和用戶端密鑰
$client_id = '839950578076-oc0kqqpmivolvijj49rq8mflvd1r3g1s.apps.googleusercontent.com';
$client_secret = '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';

// 定義重定向URI
$redirect_uri = 'http://localhost:5173';

// 定義授權範圍
$scope = 'https://www.googleapis.com/auth/gmail.compose';

// 設置授權連結
$auth_url = 'https://accounts.google.com/o/oauth2/auth';
$auth_params = array(
    'response_type' => 'code',
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => $scope
);
$auth_url .= '?' . http_build_query($auth_params);

// 檢查是否已獲取授權碼
if (isset($_GET['code'])) {
    // 獲取授權碼
    $code = $_GET['code'];

    // 交換授權碼以獲取訪問令牌
    $token_url = 'https://oauth2.googleapis.com/token';
    $token_params = array(
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // 解析響應
    $token_data = json_decode($response, true);

    // 獲取訪問令牌
    $access_token = $token_data['access_token'];
    
    // 使用訪問令牌向Gmail API發送請求
    // 在此之後，您可以使用$access_token向Gmail API發送請求

    echo 'Access Token: ' . $access_token;
} else {
    // 重定向用戶到授權頁面
    header('Location: ' . $auth_url);
    exit;
}
?>
