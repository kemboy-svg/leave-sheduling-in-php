<?php
session_start();
if(isset($_SESSION['emp_id']) || isset($_SESSION['firstname']) || isset($_SESSION['status']))
    header('Location: .');
$form=true;
$error="";
if(isset($_POST['submit']))
{
	$form=false;
	require_once 'includes/config.php';
	$dbc=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME) or die('Error');
	$email=mysqli_real_escape_string($dbc, $_POST['email']);
	if(empty($email))
	{
		$error="Please enter your email address";
		$form=true;
	}
	else
	{
		$query="select user_id from user_details where user_email='$email'";
		$result=mysqli_query($dbc, $query) or die('Error');
		if(mysqli_num_rows($result)==0)
		{
			$error="Email does not belong to any account";	
			$form=true;
		}
	}
	if(!$form)
	{
		$x=md5(rand());
		$row=mysqli_fetch_array($result,MYSQL_ASSOC);
		$uid=$row['emp_id'];
		$query="insert into temp(actual_id,code) values($uid,'$x')"; 
		$result=mysqli_query($dbc, $query);
		if(!$result)
		{
			$form=true;
			$error="You have already requested for a new password. Check you spam folders.";
		}
		else
		{
			$sub="Password Change - MLS";
			$text='Sir as per requested we are sending you a link to change your password.';
			$text.="You can change the pass by simply clicking <a href='http://mmust.ac.ke/changepass?code=".$x."'>here</a>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
        	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: Admin<no-reply@dero.com>' . "\r\n";
			mail($email, $sub, $text, $headers);
			echo 'Click on the link sent in the email.Redirecting...';
			header('refresh:2;url=.');
		}
	}
}
if($form)
{
?>
<html>
<head>
	<title>Mmust Leave System | Forget Password</title>
	<link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
	<div>
		<div>
			<h1 id="heading">MMUST LS</h1>
		</div>
	</div>

	<div id="loginbox1">
		<div id="loginbox2">
			<div id="loginbox3">
				<div id="loginbox4">
					<h2 id="loginheading">Forget Password</h2>

					
					<div id="error"> <?php echo $error; 	?></div>

					<form method="post" action="forget">
						<input type="email" name="email" id="mailfield" placeholder="Your E-Mail" required><br><br>
						<input type="submit" name="submit" value="Send" id="submitbutton"><br><br>
					</form>
					<a href="." id="forget">Log In</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<?php
}
?>