<?php
require('../general/database.php');
require('../general/StatusCode.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;
$key = 'luckyLucy';

$imageID = $_POST['imageID'];

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno()){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT travelimage.PATH,travelimage.Title,travelimage.Description,travelimage.Content,traveluser.UserName,geocountries.CountryName,geocities.AsciiName
FROM travelimage,traveluser,geocities,geocountries
WHERE travelimage.ImageID = ?
AND travelimage.UID = traveluser.UID
AND travelimage.CountryCodeISO = geocountries.ISO
AND travelimage.CityCode = geocities.GeoNameID';

$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();
$result = $stmt->get_result();

$items = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $items[] = $row;
}//$item中是结果对象数组

$query = 'SELECT travelimagefavor.FavorID
FROM travelimagefavor,travelimage
WHERE travelimage.ImageID = ?
AND travelimage.ImageID = travelimagefavor.ImageID';
$stmt = $db->prepare($query);
$stmt->bind_param('i',$imageID);
$stmt->execute();
$stmt->store_result();
$favorNum = $stmt->num_rows;//收藏的数目

if(isset($_POST['token'])){
    try {
        $decode = JWT::decode($_POST['token'], $key, array('HS256'));
    }catch (Exception $e){
        echo json_encode(array('details'=>$items,'favorNum'=>$favorNum));
        $db->close();
        exit();
    }
    $username = $decode->username;

    $query = 'SELECT travelimagefavor.FavorID FROM travelimagefavor,travelimage,traveluser
WHERE travelimagefavor.UID = traveluser.UID
AND travelimage.ImageID = travelimagefavor.ImageID
AND travelimage.ImageID = ?
AND traveluser.UserName = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('is',$imageID,$username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($favorId);
    $stmt->fetch();
    if($stmt->num_rows > 0){
        echo json_encode(array('details'=>$items,'favorNum'=>$favorNum,'collected'=>true,'favorId'=>$favorId));
    }else{
        echo json_encode(array('details'=>$items,'favorNum'=>$favorNum));
    }
    $db->close();
    exit();
}


$db->close();

echo json_encode(array('details'=>$items,'favorNum'=>$favorNum));