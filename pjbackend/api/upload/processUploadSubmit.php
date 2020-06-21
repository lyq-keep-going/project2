<?php
require('../general/StatusCode.php');
require('../general/database.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;

$imgname = $_FILES['img']['name'];
$tmp = $_FILES['img']['tmp_name'];
$filepath1 = "D:/xampp/htdocs/pjbackend/travel-images/travel-images/large/";
$filepath2 = "D:/xampp/htdocs/pjbackend/travel-images/travel-images/small/";
$imgname = $imgname.date('U');
if (move_uploaded_file($tmp, $filepath1 . $imgname) && copy($filepath1.$imgname,$filepath2.$imgname)) {
    $db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
    if(mysqli_connect_errno() ){
        https(503);
        echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
        exit();
    }
    $title = $_POST['title'];
    $description = $_POST['description'];
    $topic = $_POST['topic'];
    $country = $_POST['country'];
    $city = $_POST['city'];

    $token = $_POST['token'];
    $key = 'luckyLucy';

    try {
        $decoded = JWT::decode($token, $key, array('HS256'));
    }catch (Exception $exception){
        https(403);
        echo '请重新登录';
        exit();
    }

    $username = $decoded->username;
    $db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

    if(mysqli_connect_errno() ){
        https(503);
        echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
        exit();
    }

    $query = 'SELECT geocities.GeoNameID, geocountries.ISO, traveluser.UID FROM geocities,geocountries,traveluser
WHERE geocities.AsciiName = ? AND geocountries.CountryName = ? AND traveluser.UserName = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('sss',$city,$country,$username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cityCode,$countryCodeISO,$UID);
    $stmt->fetch();

    $query = 'INSERT INTO travelimage VALUES (null,?,?,0,0,?,?,?,?,?)';
    $stmt = $db->prepare($query);
    $stmt->bind_param('sssssss',$title,$description,$cityCode,$countryCodeISO,$UID,$imgname,$topic);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->affected_rows > 0){
        echo '上传成功';
    }else{
        echo "上传失败";
    }


} else {
    echo "上传失败";
}

$db->close();