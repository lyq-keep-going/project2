<?php
require ('./general/StatusCode.php');
require ('./general/database.php');

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT geocities.AsciiName FROM geocities,geocountries WHERE geocountries.CountryName = ? AND 
geocities.CountryCodeISO = geocountries.ISO';
$stmt = $db->prepare($query);
$stmt->bind_param('s',$_POST['country']);
$stmt->execute();
$result = $stmt->get_result();

$cities = array();

while($row = mysqli_fetch_assoc($result)){
    $cities[] = $row['AsciiName'];
}
$db->close();

echo json_encode(array('cities'=>$cities));