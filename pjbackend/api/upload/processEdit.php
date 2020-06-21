<?php
require('../general/StatusCode.php');
require('../general/database.php');
require('../php-jwt-5.2.0/src/JWT.php');
use \Firebase\JWT\JWT;
$key = 'luckyLucy';

$title = $_POST['title'];
$description = $_POST['description'];
$topic = $_POST['topic'];
$country = $_POST['country'];
$city = $_POST['city'];
$imageID = $_POST['imageID'];
$token = $_POST['token'];
//先接受数据

    try {
        $decoded = JWT::decode($token, $key, array('HS256'));
    }catch (Exception $exception){
        https(403);
        echo '请重新登录';
        exit();
    }

    $username = $decoded->username;
    //验证身份

$db = new mysqli(HOST, USERNAME, PASSWORD, 'new_travels');

if (mysqli_connect_errno()) {
    https(503);
    echo json_encode(array('message' => 'Error:Could not connect to database. Please try again later.'));
    exit();

}

if(isset($_FILES['img'])) {
    //此时要先读出数据库中原图片的path，进行删除，再将新上传的图片移到对应文件夹


    $query = 'SELECT PATH FROM travelimage WHERE ImageID = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('i',$imageID);
    $stmt->execute();
    $path = mysqli_fetch_assoc($stmt->get_result());


    $filepath1 = "../../travel-images/travel-images/large/";
    $filepath2 = "../../travel-images/travel-images/small/";

    if(!unlink($filepath1. $path['PATH']) || !unlink($filepath2. $path['PATH'])){
        https(500);
        echo '图片修改失败1';
        $db->close();
        exit();
    }

    $imgname = $_FILES['img']['name'];
    $tmp = $_FILES['img']['tmp_name'];
    echo $imgname;
    if (!move_uploaded_file($tmp, $filepath1 . $imgname) || !copy($filepath1.$imgname,$filepath2.$imgname)){
        https(500);
        echo '图片修改失败2';
        $db->close();
        exit();
    }

    $query = 'UPDATE travelimage SET PATH = ? WHERE  ImageID = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('si',$imgname,$imageID);
    $stmt->execute();
    $stmt->store_result();
//    if($stmt->affected_rows <= 0){
//        https(500);
//        echo $stmt->affected_rows;
//        echo '修改图片失败3';
//        $db->close();
//        exit();
//    }

}
//先找出countryid和cityid
$query = 'SELECT geocountries.ISO,geocities.GeoNameID
FROM geocities,geocountries
WHERE geocountries.CountryName = ?
AND geocities.AsciiName = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('ss',$country,$city);
$stmt->execute();
$codes = mysqli_fetch_assoc($stmt->get_result());


$query = 'UPDATE travelimage SET Title = ?, Description = ?,Content = ?,CityCode = ?,CountryCodeISO = ?
WHERE  ImageID = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('sssisi',$title,$description,$topic,$codes['GeoNameID'],$codes['ISO'],$imageID);
$stmt->execute();
$stmt->store_result();
//if($stmt->affected_rows <= 0){
//    https(500);
//    echo '修改图片失败4';
//    $db->close();
//    exit();
//}

echo '图片修改成功';
$db->close();

