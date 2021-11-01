<?php 
	session_start();
	if (!isset($_SESSION['username']) && !isset($_SESSION['password'])) {
		header("Location: login.php");
	}
	// fetch user details from database
	$sql_checker = true;
	include 'connection.php';
	$sql = "SELECT * FROM accounts";
	$result = mysqli_query($conn, $sql);
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
	if ($result) {
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		foreach ($arr as $user) {
			if ($username = $user['username'] && $password == $user['password']) {
				// fetch data from database
				$name = $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'];
				$birthday = $user['birthday'];
				$email = $user['email'];
				$contact = $user['contact_number'];
			}
		}
	} else {
		$sql_checker = false;
	}
	// logout button
	if (isset($_POST['logout'])) {
		session_destroy();
		header("Location: login.php");
	}
	// reset password
	$all_checker = true;
	$current_password_checker = true;
	$match_checker = true;
	$final_checker = false;
	if (isset($_POST['reset'])) {
		// check if all password fields have value
		if (empty($_POST['current_password'])) {
			$all_checker = false;
		} elseif (empty($_POST['new_password'])) {
			$all_checker = false;
		} elseif (empty($_POST['new_password_copy'])) {
			$all_checker = false;
		} else {
			$cur = sha1($_POST['current_password']);
			$new_password = sha1($_POST['new_password']);
			$new_password_copy = sha1($_POST['new_password_copy']);
			// verify the password fields
			if ($cur != $password) {
				$current_password_checker = false;
			} else if ($new_password != $new_password_copy) {
				$match_checker = false;
			} else {
				// note: re-initialize the SESSION variables
				$username = $_SESSION['username'];
				$password = $_SESSION['password'];
				// update new password
				$sql2 = "UPDATE accounts SET password = '$new_password' WHERE username = '$username' AND password = '$password'";
				$result2 = mysqli_query($conn, $sql2);
				if ($result2) {
					// don't forget to change SESSION variables
					$_SESSION['password'] = $new_password;
					$final_checker = true;
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
		<title>Welcome, <?php echo $_SESSION['username']; ?>!</title>
	</head>
	<body>
		<div class="user-container">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<div class="upper">
					<h1>User Information Form</h1>
					<?php 
						if (!$sql_checker) {
							?>
								<h2 class="err">An error occurred.</h2>
							<?php
						}
					?>
					<h2><b>Welcome</b> <?php echo $name; ?></h2>
					<h2><b>Birthday:</b> <?php echo $birthday; ?></h2>
					<h2><b>Contact Details:</b></h2>
					<h2><b>Email:</b> <?php echo $email; ?></h2>
					<h2><b>Contact:</b> <?php echo $contact; ?></h2>
					<input type="submit" name="logout" value="Logout">
				</div>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<div class="lower">
					<h1>Reset Password</h1>
					<h2>Enter Current Password</h2>
					<input type="password" name="current_password">
					<h2>Enter New Password</h2>
					<input type="password" name="new_password">
					<h2>Re-Enter New Password</h2>
					<input type="password" name="new_password_copy">
					<?php 
						if (!$sql_checker) {
							?>
								<h2 class="err">An error occurred.</h2>
							<?php
						}
						if (!$all_checker) {
							?>
								<h2 class="err">Incomplete fields.</h2>
							<?php
						}
						if (!$current_password_checker) {
							?>
								<h2 class="err">Current password is not the same with the old password.</h2>
							<?php
						}
						if (!$match_checker) {
							?>
								<h2 class="err">New password and Re-enter new password should be the same.</h2>
							<?php
						}
						if ($final_checker) {
							?>
								<h2 class="success">Password successfully changed!</h2>
							<?php
						}
					?>
					<input type="submit" name="reset" value="Reset Password">
				</div>
			</form>
		</div>
	</body>
</html>