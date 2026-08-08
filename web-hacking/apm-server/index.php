<?php

session_start();

if(!isset($_SESSION["username"])) {
	header("Location; login.php");
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Main</title>
</head>

<body>
<h2>Welcome</h2>
<p>
Hello,
<?php echo $_SESSION["username"]; ?> !
</p>

<a href="logout.php">Logout</a>
</body>
</html>
