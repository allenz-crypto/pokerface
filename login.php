<html>
<body>
<h1>Login</h1>
<form method="POST" action="login.php">
	<label>Username:</label>
	<input type="text" name="username">
	<label>Password:</label>
	<input type="password" name="password">
	<input type="submit" name="" value="Login" >
</form>

<?php

include 'user_class.php';
include 'tbs_class.php';
	if (!array_key_exists('username', $_POST) && !array_key_exists('password', $_POST)) {
		
	}
	else if (!array_key_exists('username', $_POST) || !array_key_exists('password', $_POST)) {
		echo 'Please fill in the Login form.';
	}
	else if($_POST['username'] == '' || $_POST['password'] == ''){
		echo 'Please fill in all of the form please';
	}
	else {
		$connection;
		try {
			$username = $_POST['username'];                                        
            $password = User::hash($_POST['password']);
						
			$user = new User;
			$user->setProperties(array(
				'username' => $username,
				'password' => $password
			));
			
			$user->verify();
			session_start();
			$_SESSION['USER'] = $user;
			header('Location: register.php') ;

		}
		catch (Exception $exception) {
			echo $exception->getMessage();
		}
	}
	$TBS = new clsTinyButStrong;
	//$TBS->LoadTemplate('game-history.html');
	//$TBS->MergeBlock('gameData', $gameData);
	$TBS->Show();
?>


	
</body>
</html>