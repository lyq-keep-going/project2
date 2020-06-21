<?php
require ('../general/StatusCode.php');
require ('../general/database.php');

$method = $_POST['method'];
$content = $_POST['content'];
$page = $_POST['page'];

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$tmpArray = explode(' ',$content);
$tmpString = '%';
foreach($tmpArray as $value){
    $tmpString = $tmpString.$value.'%';
}

if($method == 'title'){
    $query = 'SELECT Title,Description,PATH,ImageID FROM travelimage WHERE Title LIKE ? LIMIT ?,4';
    $stmt = $db->prepare($query);
    $start = 4*$page-4;
    $stmt->bind_param('si',$tmpString,$start);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResult = array();
    while($row = mysqli_fetch_assoc($result)){
        $searchResult[] = $row;
    }
    $query = 'SELECT Title,Description,PATH FROM travelimage WHERE Title LIKE ? ';
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$tmpString);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalPage = mysqli_num_rows($result)%4 == 0 ? (int)(mysqli_num_rows($result)/4):(int)(mysqli_num_rows($result)/4) + 1;
    echo json_encode(array('totalPage'=>$totalPage,'searchResult'=>$searchResult));
}elseif($method == 'description'){
    $query = 'SELECT Title,Description,PATH,ImageID FROM travelimage WHERE Description LIKE ? LIMIT ?,4';
    $stmt = $db->prepare($query);
    $start = 4*$page-4;
    $stmt->bind_param('si',$tmpString,$start);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResult = array();
    while($row = mysqli_fetch_assoc($result)){
        $searchResult[] = $row;
    }
    $query = 'SELECT Title,Description,PATH FROM travelimage WHERE Description LIKE ? ';
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$tmpString);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalPage = mysqli_num_rows($result)%4 == 0 ? (int)(mysqli_num_rows($result)/4):(int)(mysqli_num_rows($result)/4) + 1;
    echo json_encode(array('totalPage'=>$totalPage,'searchResult'=>$searchResult));
}

$db->close();