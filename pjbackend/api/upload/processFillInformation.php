<?php
//前端用ImageID请求，后端找到图片，根据path寻找文件返回给前端
require('../general/StatusCode.php');
require('../general/database.php');
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

if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT travelimage.Title, travelimage.Description, travelimage.PATH, travelimage.Content,geocountries.CountryName,
       geocities.AsciiName
FROM geocities,geocountries,travelimage
WHERE travelimage.ImageID = ?
AND travelimage.CityCode = geocities.GeoNameID
AND travelimage.CountryCodeISO = geocountries.ISO';
$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();
$result = $stmt->get_result();

$imageDetails = mysqli_fetch_assoc($result);

$db->close();


echo json_encode($imageDetails);
