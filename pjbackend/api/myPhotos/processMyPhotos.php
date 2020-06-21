<?php
require ('../general/database.php');
require ('../general/StatusCode.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;
$key = 'luckyLucy';

$token = $_POST['token'];
$page = $_POST['page'];
//应该返回结果数组（按照ImageID排序的）的index为4*page-4到4*page-1的结果

try {
    $decode = JWT::decode($token, $key, array('HS256'));
}catch (Exception $e){
    https(403);
    echo 'Please login again!';
    exit();
}

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT travelimage.ImageID,travelimage.Description,travelimage.Title,travelimage.PATH
FROM travelimage,traveluser
WHERE traveluser.UserName = ?
AND travelimage.UID = traveluser.UID
ORDER BY travelimage.ImageID
LIMIT ?,4'
;
$stmt = $db->prepare($query);
$start = 4*$page-4;
$stmt->bind_param('si',$decode->username,$start);
$stmt->execute();
$result = $stmt->get_result();

$myPictures = array();
//获取所有收藏图片
while ($row = mysqli_fetch_assoc($result)){
    $myPictures[] = $row;
}

$query = 'SELECT travelimage.ImageID,travelimage.Description,travelimage.Title,travelimage.PATH
FROM travelimage,traveluser
WHERE traveluser.UserName = ?
AND travelimage.UID = traveluser.UID';
$stmt = $db->prepare($query);

$stmt->bind_param('s',$decode->username);
$stmt->execute();
$result = $stmt->get_result();

$totalPage = mysqli_num_rows($result)%4 == 0 ? (int)(mysqli_num_rows($result)/4):(int)(mysqli_num_rows($result)/4) + 1;
$db->close();

echo json_encode(array('myPictures'=>$myPictures,'totalPage'=>$totalPage));


