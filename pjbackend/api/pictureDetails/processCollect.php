<?php
require('../general/database.php');
require('../general/StatusCode.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;

$token = $_POST['token'];
$imageId = $_POST['imageID'];
$key = 'luckyLucy';

try {
    $decoded = JWT::decode($token, $key, array('HS256'));
}catch (Exception $exception){
    https(403);
    echo '请重新登录';
}

$username = $decoded->username;

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}


$query = 'SELECT UID
FROM traveluser
WHERE traveluser.UserName = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($uid);
$stmt->fetch();

$query = 'INSERT INTO travelimagefavor VALUES (null,?,?)';
$stmt = $db->prepare($query);
$stmt->bind_param('ss',$uid,$imageId);
$stmt->execute();

if($stmt->affected_rows > 0){
    https(200);
    echo 'You have collected the picture!';
}else{
    https(501);
    echo 'collect fail';
}

$db->close();