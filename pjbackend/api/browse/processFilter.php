<?php
require ('../general/StatusCode.php');
require ('../general/database.php');

$content = $_POST['content'];
$city = $_POST['city'];
$country = $_POST['country'];

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
if(mysqli_connect_errno() ){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

$query = 'SELECT travelimage.PATH,travelimage.ImageID
FROM geocities,geocountries,travelimage
WHERE travelimage.Content = ?
AND geocities.AsciiName = ?
AND geocities.GeoNameID = travelimage.CityCode
AND geocountries.CountryName = ?
AND geocountries.ISO = travelimage.CountryCodeISO';

$stmt = $db->prepare($query);
$stmt->bind_param('sss',$content,$city,$country);
$stmt->execute();
$result = $stmt->get_result();

$results = array();

while($row = mysqli_fetch_assoc($result)){
    $results[] = $row;
}
$totalPage = mysqli_num_rows($result)%16 == 0 ? (int)(mysqli_num_rows($result)/16):(int)(mysqli_num_rows($result)/16) + 1;

$db->close();

echo json_encode(array('results'=>$results,'totalPage'=>$totalPage));