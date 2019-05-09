<?php

ob_start();
//testing connection to github repo
try{
    $con = new PDO("mysql:dbname=giggle;host=localhost", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}catch (PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}