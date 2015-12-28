<?php

//echo 'in CreateProfile<br>';
require_once ('/20150907/DatabaseHandler.php');
require_once ('/20150907/CRUDInterface.php');

$db = new DatabaseHandler();
$errorMessages = array();
$showStopperError = false;
$id = 1;
$status = 400; // HTTP status code

//echo "in CreateProfile version=".phpversion().'<br>';
// use school email as login username
if(isset($_POST['operation']) && isset($_POST['schoolemail']) && isset($_POST['password']))
{
	//echo 'Operation is: '. $_POST['operation'];
	$ci = new CRUDInterface();
	$ci->crud();
}
else
{
	$showStopperError = true;
	$code = 2; // Missing required authentication parameter
	$title = 'Missing authentication parameters.'; // summary of problem
	$detail = 'No operation, schoolemail, password.'; // explanation of problem
	$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
}
?>
