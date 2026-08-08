<?php
$servername = "localhost";
$username = "webuser";
$password = "1234";
$dbname = "login_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error) {
	die("connect fail : " . $conn->connect_error);
}

?>
