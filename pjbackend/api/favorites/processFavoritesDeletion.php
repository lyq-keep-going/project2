<?php
require('../general/database.php');
require('../general/StatusCode.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;
$key = 'luckyLucy';

$token = $_POST['token'];
$favorId = $_POST['favorId'];

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

$query = 'DELETE FROM travelimagefavor WHERE FavorID = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('s',$favorId);
$stmt->execute();
$stmt->store_result();

if($stmt->affected_rows > 0){
    https(200);
    echo 'You have removed the picture from collection.';
}else{
    https(501);
}

$db->close();