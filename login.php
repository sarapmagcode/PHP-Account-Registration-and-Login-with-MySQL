<?php 
	session_start();
	if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
		header("Location: user.php");
	}
	$all_checker = true;
	$sql_checker = true;
	$account_checker = true;
	if (isset($_POST['login'])) {
		// check if all fields have input
		if (empty($_POST['username'])) {
			$all_checker = false;
		} elseif (empty($_POST['password'])) {
			$all_checker = false;
		} else {
			include 'connection.php';
			$sql = "SELECT username, password FROM accounts";
			$result = mysqli_query($conn, $sql);
			if ($result) {
				$arr = array();
				// fetch data from database (username and password columns)
				while ($row = mysqli_fetch_assoc($result)) {
					$arr[] = $row;
				}
				// set variables
				$username = $_POST['username'];
				$password = sha1($_POST['password']);
				foreach ($arr as $user) {
					// verify username and password
					if ($user['username'] == $username && $user['password'] == $password) {
						$_SESSION['username'] = $username;
						$_SESSION['password'] = $password;
						// if matched, then proceed to user page
						$account_checker = true;
						header("Location: user.php");
					}
				}
				$account_checker = false;
			} else {
				$sql_checker = false;
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Login Page</title>
	</head>
	<body>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="login-form">
				<h1>Login Form</h1>
				<h3>Username</h3>
				<input type="text" name="username">
				<h3>Password</h3>
				<input type="password" name="password">
				<?php
					if (!$all_checker) {
						?>
							<h3 class="err">Insufficient fields.</h3>
						<?php
					} 
					if (!$sql_checker) {
						?>
							<h3 class="err">Error occurred.</h3>
						<?php
					}
					if (!$account_checker) {
						?>
							<h3 class="err">Incorrect username/password.</h3>
						<?php
					}
				?>
				<input type="submit" name="login" value="Login">
			</div>
		</form>
	</body>
</html>