<?php
require ('../general/StatusCode.php');
require ('../general/database.php');

$country = $_POST['country'];
$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT travelimage.PATH,travelimage.ImageID FROM travelimage,geocountries 
WHERE travelimage.CountryCodeISO = geocountries.ISO
AND geocountries.CountryName = ?';
$stmt = $db->prepare($query);
$stmt->bind_param('s',$country);
$stmt->execute();
$result = $stmt->get_result();

$results = array();

while($row = mysqli_fetch_assoc($result)){
    $results[] = $row;
}
$totalPage = mysqli_num_rows($result)%16 == 0 ? (int)(mysqli_num_rows($result)/16):(int)(mysqli_num_rows($result)/16) + 1;
$db->close();
echo json_encode(array('results'=>$results,'totalPage'=>$totalPage));