<?php

require 'db_connect.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST["username"];
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

	$sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";

	if($conn->query($sql) === TRUE) {
		header("Location: login.php");
		exit();
	} else {
		echo "Error : " . $conn->error;
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
</head>
<body>
<h2>Register</h2>
<form method="post">

	Username :
	<input type="text" name="username">

	<br><br>

	Password :
	<input type="password" name="password">

	<br><br>

	<input type="submit" value="Register">

</form>
</body>
</html>
