<?php
require ('../general/StatusCode.php');
require ('../general/database.php');

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT Content,COUNT(UID)
FROM travelimage
GROUP BY Content
ORDER BY COUNT(UID) DESC
LIMIT 0,3';

$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$hotContents = array();

while($row = mysqli_fetch_assoc($result)){
    $hotContents[] = $row;
}
$query = 'SELECT CityCode ,COUNT(UID)
FROM travelimage
GROUP BY CityCode
ORDER BY COUNT(UID) DESC
LIMIT 0,6';

$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$hotCities = array();

while($row = mysqli_fetch_assoc($result)){
    $hotCities[] = codeToCity($row['CityCode']);
}

$query = 'SELECT CountryCodeISO ,COUNT(UID)
FROM travelimage
GROUP BY CountryCodeISO
ORDER BY COUNT(UID) DESC
LIMIT 0,6';

$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$hotCountries = array();

while($row = mysqli_fetch_assoc($result)){
    $hotCountries[] = codeToCountry($row['CountryCodeISO']);
}

$db->close();

echo json_encode(array('hotContents'=>$hotContents,'hotCities'=>$hotCities,'hotCountries'=>$hotCountries));