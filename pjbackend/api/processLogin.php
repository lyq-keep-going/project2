<?php
require('./php-jwt-5.2.0/src/JWT.php');
require ('./general/database.php');
require ('./general/StatusCode.php');
require ('./general/addSalt.php');
use \Firebase\JWT\JWT;

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno()){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}
//先获得改用户盐值
$query = 'SELECT SALT FROM traveluser WHERE UserName  = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('s',$username);
$stmt->execute();
$salt = mysqli_fetch_assoc($stmt->get_result());
$password = addSalt($password,$salt['SALT']);

$query = "SELECT * FROM traveluser WHERE UserName LIKE ? and Pass LIKE ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ss',$username,$password);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    //生成token
    $key = 'luckyLucy';
    $dateNow = date('U');
    $payload = array(
        "iss"=>"http://".HOST,
        "aud"=>"http://".HOST,
        "iat"=>$dateNow,
        "nbf"=>$dateNow,
        "exp"=>$dateNow + 3600*5,//五小时过期
        "username"=>$username,
        "password"=>$password
    );
    $jwt = JWT::encode($payload, $key);

    https(200);
    echo json_encode(array('message'=>'Successful login!','token'=>$jwt,'username'=>$username));
}else{
    https(400);
    echo json_encode(array('message'=>'No such user!'));
}

$db->close();