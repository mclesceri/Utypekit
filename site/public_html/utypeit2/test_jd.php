<?php
include($_SERVER['DOCUMENT_ROOT'].'/cb/utypeit2/src/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/cb/utypeit2/src/globals.php');

//$server = "localhost";
//$uname = "marke131_cb_oms";
//$password = "nD85XQy3";


try {
    $conn = new PDO("mysql:host=$server;dbname=marke131_cb_oms", $uname, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>