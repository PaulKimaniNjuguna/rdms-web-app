<?php 
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'company_db';


//create connection
$mysqli = new mysqli($host, $user, $pass, $db);

//check connection
if($mysqli->connect_error){
    die("Connection failed: " . $mysqli->connect_error);
}
?>