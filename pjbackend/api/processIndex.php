<?php
require('./general/database.php');
require ('./general/StatusCode.php');

$visitCount = $_POST['visitCount'];

$db = new mysqli(HOST,USERNAME,PASSWORD,'new_travels');

if(mysqli_connect_errno()){
    https(503);
    echo json_encode(array('message'=>'Error:Could not connect to database. Please try again later.'));
    exit();
}

if($visitCount == 1){
    $query = 'SELECT ImageID FROM travelimagefavor';
    $stmt = $db->prepare($query);
    $stmt->execute();

    $stmt->bind_result($imageID);

    $imageFavourCount = array();

    while($stmt->fetch()){
        if(isset($imageFavourCount[$imageID])){
            $imageFavourCount[$imageID]++;
        }else{
            $imageFavourCount[$imageID] = 1;
        }
    }

    arsort($imageFavourCount);

    $hotPic = array();
    $count = 0;

    foreach ($imageFavourCount as $key => $value){
        $hotPic[] = $key;
        $count++;
        if($count >= 6) break;
    }

    if(count($hotPic) < 6){
        for($i = count($hotPic); $i < 6; $i++){
            $hotPic[$i] = mt_rand(1,82);
        }
    }

    $query = 'SELECT Title,Description,PATH,ImageID FROM travelimage WHERE ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ?';
    $stmt=$db->prepare($query);
    $stmt->bind_param('iiiiii',$hotPic[0],$hotPic[1],$hotPic[2],$hotPic[3],$hotPic[4],$hotPic[5]);
    $stmt->execute();

    $result = $stmt->get_result();
    $items = array();

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $items[] = $row;
    }


    echo json_encode($items);

}else{
    $hotPic = array();
    for($i = 0; $i < 6; $i++){
        $hotPic[$i] = mt_rand(1,82);//暂时没有考虑万一随机数重复情况
    }

    $query = 'SELECT Title,Description,PATH,ImageID FROM travelimage WHERE ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ? OR ImageID = ?';
    $stmt=$db->prepare($query);
    $stmt->bind_param('iiiiii',$hotPic[0],$hotPic[1],$hotPic[2],$hotPic[3],$hotPic[4],$hotPic[5]);
    $stmt->execute();

    $result = $stmt->get_result();
    $items = array();

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $items[] = $row;
    }

    echo json_encode($items);
}

$db->close();