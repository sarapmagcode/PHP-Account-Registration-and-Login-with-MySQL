<?php
	session_start();
	if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
		header("Location: user.php");
	}
	function checkemail($email) {
		$regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
		$email = strtolower($email);
		return preg_match($regex, $email);
	}

	function checknum($num) {
		$regex = "/^[0-9]{11}$/";
		return preg_match($regex, $num);
	}

	$all_checker = true;
	$password_checker = true;
	$sql_checker = true;
	$email_checker = true;
	$number_checker = true;
	if(isset($_POST['enter'])) {
		// set variables
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$last_name = $_POST['last_name'];
		$username = $_POST['username'];
		$password = sha1($_POST['password']);
		$password_copy = sha1($_POST['password_copy']);
		$birthday = $_POST['birthday'];
		$email = $_POST['email'];
		$contact_number = $_POST['contact_number'];

		// check if all fields have input
		if (empty($first_name)) {
			$all_checker = false;
		} elseif (empty($middle_name)) {
			$all_checker = false;
		} elseif (empty($last_name)) {
			$all_checker = false;	
		} elseif (empty($username)) {
			$all_checker = false;	
		} elseif (empty($password)) {
			$all_checker = false;	
		} elseif (empty($password_copy)) {
			$all_checker = false;
		} elseif (empty($birthday)) {
			$all_checker = false;
		} elseif (empty($email)) {
			$all_checker = false;
		} elseif (empty($contact_number)) {
			$all_checker = false;
		} else {
			if ($password != $password_copy) {
				// password must be the equal to confirm password
				$password_checker = false;
			} elseif (!checkemail($email)) {
				// check email format
				$email_checker = false;
			} elseif (!checknum($contact_number)) {
				// number must be exactly 11 digits
				$number_checker = false;
			} else {
				include 'connection.php';
				// insert registration details in database
				$sql = "INSERT INTO accounts(first_name, middle_name, last_name, 
						username, password, birthday, email, contact_number)
				VALUES ('$first_name', '$middle_name', '$last_name', '$username',
						'$password', '$birthday', '$email', '$contact_number')";
				$result = mysqli_query($conn, $sql);
				if ($result) {
					header('Location: login.php');
				} else {
					$sql_checker = false;
				}
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
		<title>Registration Module</title>
	</head>
	<body>
		<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
			<div class="container">
				<h1>My Personal Information</h1>
				<h2>First Name</h2>
				<input type="text" name="first_name">
				<h2>Middle Name</h2>
				<input type="text" name="middle_name">
				<h2>Last Name</h2>
				<input type="text" name="last_name">
				<h2>Username</h2>
				<input type="text" name="username">
				<h2>Password</h2>
				<input type="password" name="password">
				<h2>Confirm Password</h2>
				<input type="password" name="password_copy">
				<h2>Birthday</h2>
				<input type="text" name="birthday">
				<h2>Email</h2>
				<input type="text" name="email">
				<h2>Contact Number</h2>
				<input type="text" name="contact_number">
				<?php 
					if (isset($_POST['enter'])) {
						if (!$password_checker) {
							?>
								<h2 class="err">Password and Confirm Password are not the same.</h2>
							<?php
						} 
						if (!$all_checker) {
							?>
								<h2 class="err">Incomplete fields.</h2>
							<?php
						}
						if (!$sql_checker) {
							?>
								<h2 class="err">Error occurred.</h2>
							<?php
						}
						if (!$email_checker) {
							?>
								<h2 class="err">Invalid email.</h2>
							<?php
						}
						if (!$number_checker) {
							?>
								<h2 class="err">Invalid contact number(11 digits).</h2>
							<?php
						}
					}
				?>
				<input type="submit" name="enter" value="Submit and Proceed">
			</div>
		</form>
	</body>
</html>
