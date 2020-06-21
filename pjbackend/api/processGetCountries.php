<?php

require ('./general/StatusCode.php');
require ('./general/database.php');

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT CountryName FROM geocountries';
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$countries = array();

while($row = mysqli_fetch_assoc($result)){
    $countries[] = $row['CountryName'];
}

$db->close();

echo json_encode(array('countries'=>$countries));