<?php
require('../general/database.php');
require('../general/StatusCode.php');
require ('../general/addSalt.php');

//接收数据
$username =trim( $_POST['username']);
$mailbox = trim($_POST['mailbox']);
$password = trim($_POST['password']);

try {
    //连接数据库
    $db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

    if(mysqli_connect_errno() ){
        https(503);
        echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
        exit();
    }


    //检查用户名是否唯一
    $query = "SELECT * FROM traveluser WHERE UserName LIKE ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        https(403);
        echo json_encode(array('message'=>'The username has already been registered.'));
        $db->close();
        exit();
    }

    //加密
    $salt = generateSalt();
    $password = addSalt($password,$salt);
    //将注册信息存入数据库
    $initialDate = date('y-m-j G-I-i-s');
    $query = "INSERT INTO traveluser VALUES (NULL,?,?,?,?,1,?,?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ssssss',$mailbox,$username,$password,$salt,$initialDate,$initialDate);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        https(200);
        echo json_encode(array('message'=>'You have registered succesfully')) ;
    }else{
        https(403);
        echo json_encode(array('message'=>'Register failed.')) ;
    }

    $db->close();

} catch (Exception $e) {
    https(500);
    echo json_encode(array('message'=>"Error:".$e->getMessage())) ;
    exit();
}

