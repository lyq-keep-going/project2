<?php


const HOST = 'localhost';
const USERNAME = 'luoyuqi';
const PASSWORD = 'luoyuqi';

function codeToCity($code){
    $db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
    if(mysqli_connect_errno() ){
        https(503);
        echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
        exit();
    }
    $query = 'SELECT AsciiName FROM geocities WHERE GeoNameID = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('i',$code);
    $stmt->execute();
    $cityName = mysqli_fetch_assoc($stmt->get_result());
    $db->close();
    return $cityName['AsciiName'];
}

function codeToCountry($code){
    $db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');
    if(mysqli_connect_errno() ){
        https(503);
        echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
        exit();
    }
    $query = 'SELECT CountryName FROM geocountries WHERE ISO = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('s',$code);
    $stmt->execute();
    $countryName = mysqli_fetch_assoc($stmt->get_result());
    $db->close();
    return $countryName['CountryName'];
}