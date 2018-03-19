<?php
	session_start();
	$db = mysqli_connect("localhost", "root", "", "intetics_db");
	if (isset($_POST['signup'])) {
		$username = $_POST['username']; 
		if(!preg_match("/[a-zA-Z]/", $username)) die("Bad username");
		$email = $_POST['email'];
		if(!preg_match("/[a-zA-Z0-9_]{3,20}@[-a-zA-Z0-9]{2,64}\.[a-zA-Z\.]{2,9}/", $email))
			die("Bad e_mail");		
		$password = $_POST['password']; 
		$password2 = $_POST['password2'];
		if($password === "" or $password !== $password2)
			die("Bad password!");
		$sql = "SELECT id FROM users  WHERE e_mail='$email' AND pswd='$password';";
		if( !($res = mysqli_query($db, $sql)) || !($row = mysqli_fetch_array($res)) ) {
			$sql = "INSERT INTO `users` (`Name` ,`E_mail`, `Pswd` ) VALUES ('$username', '$email', '$password')";
			if($res = mysqli_query($db, $sql)) {
				$_SESSION['username1'] = $username;
				$sql = "SELECT id FROM users  WHERE e_mail='$email' AND pswd='$password';";
				if($res = mysqli_query($db, $sql))
					if($row = mysqli_fetch_array($res)) {
						$_SESSION['username1'] = $username;
						$_SESSION['userid1'] = $row['id'];
						echo ('<meta http-equiv="refresh" content="0; URL=preview.php">');
					} else {
						die("Error!");
					}
			}
		} else {
				echo ('This user already exists!');
		}
		
	}
?>
<html>
	<head>
      <title>Test task 1-1</title>
      <link rel="stylesheet" type="text/css" href="formstyle.css" />
	</head>
	<body>
  
	<div class="container">
		<form method="POST" action="registration.php" enctype="multipart/form-data">
			<h1>Sign Up</h1>
			<div class="dws-input">
				<input type="text" name="username" placeholder="Username">
			</div>
			<div class="dws-input">
				<input type="email" name="email" placeholder="E-mail">
			</div>
			<div class="dws-input">
				<input type="password" maxlength="6" name="password" placeholder="Password">
			</div>
			<div class="dws-input">
				<input type="password" maxlength="6"  name="password2" placeholder="Retype password">
			</div>
			<button type="submit" name="signup" class="btn">Sign me up</button>
		</form>
	</div>
  
	</body>
</html>