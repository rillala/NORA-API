<?php
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json;charset=UTF-8');

// 連接資料庫
require_once("./connect_chd104g1.php");

//無尾熊寫法 無法辨識帳號
// $login_account = empty( $_GET["acc"] ) ? ( $_POST["acc"] ?? "" ) : $_GET["acc"];
// $login_psw = empty( $_GET["psw"] ) ? ( $_POST["psw"] ?? "" ) : $_GET["psw"];

// if($login_account != "" && $login_psw != "") {
//     $sql = " SELECT * FROM admin WHERE acc = '{$login_account}' OR psw = '{$login_psw}'; ";
//     $result = $pdo->query($sql);
//     $resArray = $result->fetch(PDO::FETCH_ASSOC);
//     $psw = $resArray["psw"]??"";

//     if($psw == $login_psw) {
//         $nowTime = time();
//         session_start();
//         $_SESSION = $resArray;
//         $result_array = ["code"=>"1", "msg"=>"登入成功",'memInfo'=>$_SESSION,'session_id'=>session_id()];
//         echo json_encode($result_array);
//     }else {
//         $result_array = ["code"=>"0", "msg"=>"帳號或密碼錯誤"];
//         echo json_encode($result_array);
//     }
// }else {
//     $result_array = ["code"=>"0", "msg"=>"帳號或密碼錯誤"];
//     echo json_encode($result_array);
// }    


try {
    // 前端以 POST 方法提交了 acc 和 psw
    $acc = $_POST['acc'];
    $psw = $_POST['psw'];

    $sql = "SELECT * FROM admin WHERE acc = :acc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':acc' => $acc]); 
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin !== false && ($psw === $admin['psw'])) {
        // 密碼驗證成功，生成並回傳 token
        $token = bin2hex(random_bytes(16));
        echo json_encode(['success' => true, 'message' => '登入成功', 'name'=>$admin['name'] ,'adminid'=>$admin['adminid'],'status'=>$admin['status'],'token' => $token]);
    } else {
        // 密碼驗證失敗或帳號不存在
        echo json_encode(['success' => false, 'message' => '帳號密碼錯誤或不存在']);
    }
} catch (PDOException $e) {
    // 捕獲 PDOException 並返回錯誤訊息
    echo json_encode(['error' => true, 'message' => '伺服器回應錯誤：' . $e->getMessage()]);
}

?>
