<?php
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');
define('DB_NAME', '');

class DatabaseHandler
{
	private $errorMessages = array();
	private $showStopperError = false;
	private $id = 1;
	private $status = 400; // HTTP status code
	
	function __construct()
	{
		//echo 'In DatabaseHandler construct<br>';
	}
	
	protected function connect()
	{
		//echo 'In DatabaseHandler connect<br>';
		return new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	}
	
	public function create_userprofile($schoolemail, $password)
	{
		//echo 'In function: create_user...<br>';
		$query = "INSERT INTO user(schoolemail, password, timejoined) VALUES (?,?,now())";
		
		$db = $this->connect();
		$statement = $db->prepare($query);
		
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/*
		else
		{
			echo 'prepare() is ok!<br>';
		}//*/
		
		$checkVar = $statement->bind_param('sss', $schoolemail, md5($password));
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$newuserId = 0;
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}
		else
		{
			$newuserId = $statement->insert_id;
			//echo 'execute() is ok! ID of last inserted record:'.$newuserId.'<br>';
		}
		/*
		//*/
		$statement->free_result();
		$db->close();
		
		return $newuserId;
	}
	
	public function authenticate_user($userSchoolEmail, $userPassword)
	{
		$query = "SELECT user_id, password FROM user WHERE schoolemail = ?";
		$db = $this->connect();
		$statement = $db->prepare($query);
		
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}
		
		$checkVar = $statement->bind_param('s', $userSchoolEmail);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}
		
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}
		
		$statement->bind_result($userId, $password);
		$statement->fetch();
		
		$isAuthenticated = false;
		if(strcmp($userPassword, $password) != 0)
		{
			$userId = 0;
		}
		
		$statement->free_result();
		$db->close();
		
		return $userId;
	}
	
	public function select_userprofile($userId)
	{
		//echo 'In function: select_userprofile...<br>';
		$query = "SELECT * FROM user where user_id= ?";
		$db = $this->connect();
		$statement = $db->prepare($query);
		
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/*
		else
		{
			echo 'prepare() is ok!<br>';
		}//*/
		
		$checkVar = $statement->bind_param('i', $userId);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'execute() is ok!<br>';
		}//*/
		
		$statement->bind_result($name, $schoolemail, $facultyid, $gender, $timejoined, $aboutme, $password, $mobile, $email,$locationshared, $photourl, $levelpt, $leveltitle, $lastloggedlocation);
		$statement->fetch();
		$profile[] = array('name'=>$name, 'schoolemail'=>$schoolemail, 'facultyid'=>$facultyid, 'gender'=>$gender, 'timejoined=>'$timejoined, 'aboutme'=>$aboutme, 'password'=>$password, 'mobile'=>$mobile, 'email'=>$email,'locationshared'=>$locationshared, 'photourl'=>$photourl, 'levelpt'=>$levelpt, 'leveltitle'=>$leveltitle, 'lastloggedlocation'=>$lastloggedlocation);
		$statement->free_result();
		$db->close();
		
		return $profile;
	}
		
	public function update_userprofile($name, $facultyid, $gender, $aboutme, $mobile, $email,$locationshared, $photourl)
	{
		echo 'In function: update_user...<br>';
		$query = "UPDATE user SET name = ?, facultyid = ?, gender = ?, aboutme = ?, mobile = ?, email = ?, locationshared = ?, photourl = ? WHERE user_id = ?";
		
		$db = $this->connect();
		$statement = $db->prepare($query);
		
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/*
		else
		{
			echo 'prepare() is ok!<br>';
		}//*/
		
		$checkVar = $statement->bind_param('ssisisisi', $name, $facultyid, $gender, $aboutme, $mobile, $email,$locationshared, $photourl, $userId);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$statement->execute();
		$checkVar = $statement->affected_rows;
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('affected_rows failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'affected_rows is ok! <br>';
		}//*/
		$statement->free_result();
		$db->close();
		
		return $checkVar;
	}
	
	// user_friends
	public function create_friendship($userId, $friendId)
	{
		//echo 'In function: create_friendship...<br>';
		$query = "INSERT INTO friendship (uid_1, uid_2, status, requestdate) VALUES (?, ?, 0, now());";
		$db = $this->connect();
		$statement = $db->prepare($query);
		
		if($statement === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/*
		else
		{
			echo 'prepare() is ok!<br>';
		}//*/
		
		$checkVar = $statement->bind_param('ii', $userId, $friendId);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$newfriendshipId = 0;
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}
		else
		{
			$newfriendshipId = $statement->insert_id;
			//echo 'execute() is ok! ID of last inserted record:'.$newfriendshipId.'<br>';
		}

		$statement->free_result();
		$db->close();
		
		return $newfriendshipId;
	}
	
	
	public function accept_friendship($user_id,$user_fid_id)
	{
		//echo 'In function: update_friendship...<br>';
		$query = "UPDATE friendship SET status = 1, approvedate = now() WHERE uid_1 = ? AND uid_2 = ?";
		
		$db = $this->connect();
		$statement = $db->prepare($query);
		if($statement === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/*
		else
		{
			echo 'prepare() is ok!<br>';
		}//*/
		
		$checkVar = $statement->bind_param('ii',$user_fid_id, $user_id);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/*
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$statement->execute();
		$rows = $statement->affected_rows;
		$outcome = false;
		if($rows < 0)
		{
			$showStopperError = true;
			$code = 33; // Database error: affected rows failed statement
			$title = 'Database error: affected rows failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('affected_rows failed: '.htmlspecialchars($statement->error));
		}
		else
		{
			$outcome = true;
			//echo 'affected_rows is ok!'. $rows.' <br>';
		}
		/*
		//*/
		$statement->free_result();
		$db->close();
		
		return $outcome;
	}
	

	// complete mission
	public function complete_mission($userId, $mission_id, $comments, $point, $photourl)
	{
		//echo 'In function: complete_mission...<br>';
		$query = "INSERT INTO missioncompleted(uid, mid, completedate, pointsearned,comments,photourl) values (?,?,now(),?,?,?)";
		$db = $this->connect();
		$statement = $db->prepare($query);
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/* 
		else 
		{ 
			echo 'prepare() is ok!<br>'; 
		}//*/
		
		$checkVar = $statement->bind_param('iiiss', $userId, $mission_id, $point, $comments, $photourl);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/* 
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$newMissionCompleted = 0;
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}
		else
		{
			$newWorkoutId = $statement->insert_id;
			//echo 'execute() is ok!<br>';
		}
		
		$statement->free_result();
		$db->close();
		
		return $newMissionCompleted;
	}
	
	// location visited mission
	public function visit_location($userId, $location_id)
	{
		//echo 'In function: create_workout...<br>';
		$query = "INSERT INTO locationvisited(uid, lid, completedate, firstvisited) values (?,?,now())";
		$db = $this->connect();
		$statement = $db->prepare($query);
		if($statement === false)
		{
			$showStopperError = true;
			$code = 31; // Database error: prepare statement
			$title = 'Database error: prepare() failed.'; // summary of problem
			$detail = htmlspecialchars($db->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('prepare() failed: '.htmlspecialchars($db->error));
		}/* 
		else 
		{ 
			echo 'prepare() is ok!<br>'; 
		}//*/
		
		$checkVar = $statement->bind_param('ii', $userId, $location_id);
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 32; // Database error: bind_param statement
			$title = 'Database error: bind_param() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('bind_param() failed: '.htmlspecialchars($statement->error));
		}/* 
		else
		{
			echo 'bind_param() is ok!<br>';
		}//*/
		
		$newLocationVisted = 0;
		$checkVar = $statement->execute();
		if($checkVar === false)
		{
			$showStopperError = true;
			$code = 33; // Database error: execute statement
			$title = 'Database error: execute() failed.'; // summary of problem
			$detail = htmlspecialchars($statement->error); // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('execute() failed: '.htmlspecialchars($statement->error));
		}
		else
		{
			$newLocationVisted = $statement->insert_id;
			//echo 'execute() is ok!<br>';
		}
		
		$statement->free_result();
		$db->close();
		
		return $newLocationVisted;
	}
	
	
}

?>
