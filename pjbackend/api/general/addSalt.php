<?php
function generateSalt(){
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );

    $length = mt_rand(50,70);
    $charsLen = count($chars) - 1;
    shuffle($chars);                            //打乱数组顺序
    $salt = '';
    for($i=0; $i<$length; $i++){
        $salt .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
    }

    return $salt;
}

function addSalt($password,$salt){
    return sha1(sha1($password).$salt);
}