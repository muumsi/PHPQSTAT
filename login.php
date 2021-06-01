<?php
$user  = $_POST['uname'];
$pass = $_POST['psw'];
include('Net/SSH2.php');

session_start();

include 'phpqstat_inc.php';

$ssh= new Net_SSH2($rlogin_hostname);
if ($ssh->login($user, $pass)) 
{
	$_SESSION['user'] = $user;
	$_SESSION['pass'] = $pass;
        header("Location: prva.php"); // Redirect browser 
} else 
{
	header("Location: world.php"); // Redirect browser 
	exit();
}

?>
