

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 



$arr = array('number' => 123); // 更改为数组以便于json_encode
echo json_encode($arr); // 将数组转换为JSON格式

?>
</body>
</html>