<?php
require ('../general/database.php');
require ('../general/StatusCode.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;
$key = 'luckyLucy';

$token = $_POST['token'];
$imageID = $_POST['imageID'];

try {
    $decode = JWT::decode($token, $key, array('HS256'));
}catch (Exception $e){
    https(403);
    echo 'Please login again!';
    exit();
}

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

//先查找图片存放的path
$query = 'SELECT PATH FROM travelimage WHERE ImageID = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();
$result = $stmt->get_result();

$path = array();//$path[0]['PATH']

while($row = mysqli_fetch_assoc($result)){
    $path[] = $row;
}

$query = 'DELETE FROM travelimage WHERE ImageID = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();
$stmt->store_result();

if($stmt->affected_rows == 0){
    https(500);
    echo '图片删除失败1';
    $db->close();
    exit();
}

$query = 'DELETE FROM travelimagefavor WHERE ImageID = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();

//已下删除图片在本地的文件
$filepath1 = "../../travel-images/travel-images/large/";
$filepath2 = "../../travel-images/travel-images/small/";

if(unlink($filepath1.$path[0]['PATH']) && unlink($filepath2.$path[0]['PATH'])){
    echo '图片删除成功';
}else{
    https(500);
    echo '图片删除失败2';
}

$db->close();