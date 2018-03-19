<?php
	session_start();
	$db = mysqli_connect("localhost", "root", "", "intetics_db");
	if (isset($_POST['signin'])) {
		$email = $_POST['email']; 
		$password = $_POST['password']; 
		$sql = "SELECT id, name FROM users  WHERE e_mail='$email' AND pswd='$password';";
		if($res = mysqli_query($db, $sql)) {
			$row = mysqli_fetch_array($res);
			if($row != 0) {
				$_SESSION['username1'] = $row['name'];
				$_SESSION['userid1'] = $row['id'];
				$_SESSION['search_tag'] = "All";
				
				echo ('<meta http-equiv="refresh" content="0; URL=preview.php">');
			} else {
				echo ("Нет такого пользователя");
			}
		}
		
	}
?>
<html>
 <head>
      <title>Test task 1-2</title>
      <link rel="stylesheet" type="text/css" href="formstyle.css" />
 </head>
  <body>
  
	<div class="container">
	<form method="POST" action="index.php" enctype="multipart/form-data">
		<h1>Sign In</h1>
		<div class="dws-input">
			<input type="text" name="email" placeholder="Email Address">
		</div>
		<div class="dws-input">
			<input type="text" name="password" placeholder="Password">
		</div>
		<button type="submit" name="signin" class="btn2">Login</button>
	</form>
	</div>
	<h2>Don't have an account ? <a href="registration.php">Signup</a></h2>
  </body>
</html>