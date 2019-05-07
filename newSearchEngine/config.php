<?php
//out put buffering is started
ob_start();

//try and start database, return connnection failed if the database doesn't connect
try {
    $con = new PDO('mysql:host=127.0.0.1;dbname=searchengine','root','');
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    
} catch(PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
}
?>