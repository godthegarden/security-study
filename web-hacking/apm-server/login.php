<?php
require 'db_connect.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$sql = "SELECT * FROM user WHERE username='$username'";

	$result = $conn->query($sql);

	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		if(password_verify($password, $row["password"])) {
			session_start();
			$_SESSION["username"]=$username;

			header("Location: index.php");
			exit();
		} else {
			echo "Wrong Password";
		}
	} else {
		echo "User NOT FOUND";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
</head>

<body>

<h2>Login</h2>

<form method="POST">
	Username :
	<input type="text" name="username">

	<br><br>

	Password :
	<input type="password" name="password">

	<br><br>

	<input type="submit" value="Login">
</form>

<br>
<a href="register.php">Register</a>
</body>
</html>
