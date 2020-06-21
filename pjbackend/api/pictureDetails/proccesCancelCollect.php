<?php
require('../general/StatusCode.php');
require('../general/database.php');

$favorId = $_POST['favorId'];
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
    echo 'You have cancelled the collection';
}else{
    https(501);
    echo 'Cancellation fail.';
}

$db->close();