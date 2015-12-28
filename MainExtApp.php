<?php

require_once ('/20150830/DatabaseHandler.php');
require_once ('/20150830/CRUDInterface.php');

$isAuthenticated = false;
$db = new DatabaseHandler();
$errorMessages = array();
$showStopperError = false;
$id = 1;
$status = 400; // HTTP status code
	
//echo "in MainExternalApp Authentication=false version=".phpversion().'<br>';
if(!isset($_POST['schoolemail']))
{
	$showStopperError = true;
	$code = 2; // Missing required authentication parameter
	$title = 'Missing required authentication parameters.'; // summary of problem
	$detail = 'No email parameter.'; // explanation of problem
	$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
}

if(!isset($_POST['password']))
{
	$showStopperError = true;
	$code = 2; // Missing required authentication parameter
	$title = 'Missing required authentication parameters.'; // summary of problem
	$detail = 'No password parameter.'; // explanation of problem
	$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
}

if(!$showStopperError)
{
	$userId = $db->authenticate_user($_POST['schoolemail'], md5($_POST['password']));
	//echo $outcome.' is userId.<br>';
	if($userId)
	{
		$isAuthenticated = true;
		//echo $_POST['email'].' is authenticated.<br>';
		
		if($_POST['operation'] == 'authenticate_user')
		{
			// 2: logged in
			$activityType = 2;
			$itemId = $db->create_activity($userId, $activityType, $userId);
			$profile = array('id'=>$userId, 'schoolemail'=>$_POST['schoolemail'], 'password'=>$_POST['password']);
			$data = array('profile'=>$profile);
			print json_encode($data);
		}
	}	
	else
	{
		//echo $_POST['email'].' is not authenticated.<br>';
		$code = 41; // Invalid parameter 
		$title = 'Invalid parameter.'; // summary of problem
		$detail = 'Invalid username or password.'; // explanation of problem
		$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
		
		$errorResponse = array();
		$errorResponse['errorResponse'] = array('errors'=>$errorMessages);
		print json_encode($errorResponse);
	}
}
else
{
	$errorResponse = array();
	$errorResponse['errorResponse'] = array('errors'=>$errorMessages);
	print json_encode($errorResponse);
}

if($isAuthenticated && ($_POST['operation'] != 'authenticate_user'))
{
	//echo 'in isAuthenticated if-statement<br>';
	$ci = new CRUDInterface();
	$ci->crud();
}
?>
