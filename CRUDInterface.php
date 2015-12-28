<?php
require_once('DatabaseHandler.php');
/*
define('','No attributes sent');
define('','Missing attributes');
define('','Operation not supported');
define('','');
define('','');
define('','Format not supported');
//*/
class CRUDInterface
{
	function __construct()
	{
		//echo 'In CRUDInterface construct<br>';
	}
	
	public function crud()
	{
		$errorMessages = array();
		$showStopperError = false;
		$id = 1;
		$status = 400; // HTTP status code
		$db = new DatabaseHandler();
		
		if(sizeof($_POST) > 0)
		{
			// initialize data
			$data = array();
			$requester_credentials = $_POST['key'];
			
			foreach($_POST as $key => $value)
			{
				$query[$key] = $value;
			}
			
			// User related tasks 
			// create user
			if (isset($query['operation']) && $query['operation'] == 'create_user')
			{
				if(isset($query['schoolemail']) && isset($query['password']))
				{
					// 1: joined Zest
					$activityType = 1;
					$data['userId'] = $db->create_userprofile($query['schoolemail'], $query['password'],);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: schoolemail, password'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes:  schoolemail, password');
				}
			}
			// logging in user 
			/*if (isset($query['operation']) && $query['operation'] == 'authenticate_user')
			{
				if(isset($query['userId']) && isset($query['password']))
				{
					$data['result'] = $db->authenticate_user($query['userId'] && $query['password']);
				}
				else
				{
					die('Missing attributes');
				}
			}//*/
			// select user 
			else if (isset($query['operation']) && $query['operation'] == 'select_user')
			{
				if(isset($query['userId']) && $query['userId'])
				{
					//echo 'in select_user<br>';
					$data['userProfile'] = $db->select_userprofile($query['userId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					//die('Missing attribute: userId');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_user_profile_points')
			{
				if(isset($query['userId']) && isset($query['date']) && $query['userId'] && $query['date'])
				{
					$user = $db->select_userprofile($query['userId']);
					$totalPoints = $db->select_points_byuserid($query['userId']);
					$dayPoints = $db->select_userworkoutpoints_byuserid_date($query['userId'], $query['date']);
					$currentRank = 0;
					$caloriesBurned = $db->select_userworkoutcalories_byuserid_date($query['userId'], $query['date']); // TODO: Must be based on person body and activity level
					$caloriesConsumed = $db->select_usermealcalories_byuserid_date($query['userId'], $query['date']);
					$data['result'] = array('user_profile'=>$user, 'date'=>$query['date'], 'total_points'=>$totalPoints, 'day_points'=>$dayPoints, 'current_rank'=>$currentRank, 'calories_burned'=>$caloriesBurned, 'calories_consumed'=>$caloriesConsumed);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					//die('Missing attribute: userId');
				}
			}
			// update user profile
			else if (isset($query['operation']) && $query['operation'] == 'update_user_profile')
			{
				if(isset($query['userId']) && isset($query['firstname']) && isset($query['lastname']) && isset($query['email']) && isset($query['password']) && isset($query['sex']) && isset($query['height']) && isset($query['weight']) && isset($query['birth']) && isset($query['level']) && isset($query['rank']))
				{
					// 29: edited profile
					$activityType = 29;
					$data['result'] = $db->update_userprofile($query['userId'], $query['firstname'], $query['lastname'], $query['email'],  md5($query['password']), $query['sex'], $query['height'], $query['weight'], $query['birth'], $query['level'], $query['rank']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: userId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attribute: userId.');
				}
			}
			// update user image
			else if (isset($query['operation']) && $query['operation'] == 'update_user_image')
			{
				if(isset($query['userId']) && $query['userId'] && isset($query['pictureURL']) && $query['pictureURL'])
				{
					// 29: edited profile
					$activityType = 29;
					$data['result'] = $db->update_user_image($query['userId'], $query['pictureURL']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: userId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attribute: userId.');
				}
			}			
			// delete user profile
			/*else if (isset($query['operation']) && $query['operation'] == 'delete_user')
			{
				if(isset($query['userId']))
				{
					$data['result'] = $db->delete_userprofile($query['userId']);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: userId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attribute: userId.');
				}
			}
			//*/
			// user points
			else if (isset($query['operation']) && $query['operation'] == 'select_points_byuserid')
			{
				if(isset($query['userId']) && $query['userId'])
				{
					$points = $db->select_points_byuserid($query['userId']);
					$data = array();
					$data['result'] = array('userId'=>$query['userId'], 'points'=>$points);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_userworkoutpoints_byuserid_date')
			{
				if(isset($query['userId']) && isset($query['dateWorkedout']) && $query['userId'] && $query['dateWorkedout'])
				{
					$total_points = $db->select_userworkoutpoints_byuserid_date($query['userId']);
					$data = array();
					$data['result'] = array('userId'=> $query['userId'], 'points'=>$total_points, 'date'=>$query['dateWorkedout']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, dateWorkedout.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_usermealpoints_byuserid_date')
			{
				if(isset($query['userId']) && isset($query['dateConsumed']) && $query['userId'] && $query['dateConsumed'])
				{
					$points = $db->select_usermealpoints_byuserid_date($query['userId']);
					$data = array();
					$data['result'] = array('userId'=>$query['userId'], 'total_points'=>$points, 'date'=>$query['dateConsumed']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, dateConsumed.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_userworkoutcalories_byuserid_date')
			{
				if(isset($query['userId']) && isset($query['dateWorkedout']) && $query['userId'] && $query['dateWorkedout'])
				{
					$calories = $db->select_userworkoutcalories_byuserid_date($query['userId']);
					$data = array();
					$data['result'] = array('userId'=>$query['userId'], 'calories'=>$calories, 'date'=>$query['dateWorkedout']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, dateWorkedout.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_usermealcalories_byuserid_date')
			{
				if(isset($query['userId']) && isset($query['dateConsumed']) && $query['userId'] && $query['dateConsumed'])
				{
					$data['total_points'] = $db->select_usermealcalories_byuserid_date($query['userId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, dateConsumed.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if(isset($query['operation']) && $query['operation'] == 'select_userpointscalories_byuserid_date')
			{
				if(isset($query['userId']) && isset($query['date']) && $query['userId'] && $query['date'])
				{
					$totalPoints = $db->select_points_byuserid($query['userId']);
					$dayPoints = $db->select_userworkoutpoints_byuserid_date($query['userId'], $query['date']);
					$currentRank = 0;
					$caloriesBurned = $db->select_userworkoutcalories_byuserid_date($query['userId'], $query['date']); // TODO: Must be based on person body and activity level
					$caloriesConsumed = $db->select_usermealcalories_byuserid_date($query['userId'], $query['date']);
					$data['result'] = array('userId'=>$query['userId'], 'date'=>$query['date'], 'total_points'=>$totalPoints, 'day_points'=>$dayPoints, 'current_rank'=>$currentRank, 'calories_burned'=>$caloriesBurned, 'calories_consumed'=>$caloriesConsumed);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, date.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			} 
			else if(isset($query['operation']) && $query['operation'] == 'select_ranking')
			{
				if(isset($query['dateMonday']) && isset($query['dateToday']) && $query['dateMonday'] && $query['dateToday'])
				{
					$data['result'] = $db->select_ranking($query['dateMonday'], $query['dateToday']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: dateMonday, dateToday.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// user badges
			
			// user_friends
			else if (isset($query['operation']) && $query['operation'] == 'send_friend_request')
			{
				// 28: sent a friend request
				$activityType = 28;	
				if(isset($query['userId']) && isset($query['friendId']) && $query['userId'] && $query['friendId'])
				{
					$data['friendshipId'] = $db->create_friendship($query['userId'], $query['friendId']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['friendshipId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, friendId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					//die('Missing attributes: userId.');
				}
			}
			// view friends list
			else if (isset($query['operation']) && $query['operation'] == 'view_friends')
			{
				if(isset($query['userId']) && isset($query['statusId']) && $query['userId'] && $query['statusId'])
				{
					$data['friends'] = $db->select_friendship($query['userId'], $query['statusId']);
					//$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['result']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, statusId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					//die('Missing attributes');
				}
			}
			// acknowledge friend request
			else if (isset($query['operation']) && $query['operation'] == 'acknowledge_friend_request')
			{
				if(isset($query['friendshipId']) && $query['friendshipId'])
				{
					$statusId = 2;
					$activityType = 19;
					$data['result'] = $db->update_friendship($query['friendshipId'], $statusId, date('Y-m-d H:i:s'));
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['friendshipId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, friendshipId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					//die('Missing attributes');
				}
			}
			/*
			else if (isset($query['operation']) && $query['operation'] == 'drop_friend')
			{
				if(isset($query['friendshipId']))
				{
					$statusId = 3;
					$activityType = 20;
					$data['result'] = $db->update_friendship($query['friendshipId'], $statusId);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['friendshipId']);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: friendshipId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			//*/
			
			// Workouts related tasks 
			/*
			// create new workout
			if (isset($query['operation']) && $query['operation'] == 'create_workout')
			{
				// 3,"logged a workout"
				$activityId = 3;
				if(isset($query['userId']) && isset($query['exerciseCategoryId']) && isset($query['performedDate']))
				{
					$data['result'] = $db->create_workout($query['userId'], $query['exerciseCategoryId'], $query['performedDate']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userId']);
				}
				else
				{
					die('Missing attributes');
				}
				
			}
			//*/
			else if (isset($query['operation']) && $query['operation'] == 'create_conditioning_exercise_sets')
			{
				//echo 'create_conditioning_exercise_sets: userId '.$query['userId'].' sets '.$query['sets'].'<br>';
				//3: logged a workout
				$activityType = 3;
				if(isset($query['userId']) && isset($query['performed']) && isset($query['exerciseId']) && isset($query['sets']) && $query['userId'] && $query['performed'] && $query['exerciseId'] && $query['sets'])
				{
					$points = 0;
					$calories = 0;
					$userId = $query['userId'];
					$performed = $query['performed'];
					$exerciseId = $query['exerciseId'];
					$encoded_string = $query['sets'];
					$json_string = html_entity_decode($encoded_string);
					//echo 'decoded string:'.$json_string.'<br>';
					$exercise_sets = json_decode($json_string, true);
					
					$best_weight = 0;
					$best_repetition = 0;
					$best_performance = $db->select_conditioning_exercises_bytypeId_orderbyperformance($exerciseId, $userId);
					if(!empty($best_performance)) 
					{
						//echo 'best_performance: weight '.$best_performance['weight'].' reps '.$best_performance['repetition']. ' points '.$best_performance['points']. ' personal record '.$best_performance['personalRecord'].'<br>';
						$best_weight = $best_performance['weight'];
						$best_repetition = $best_performance['repetition'];
					}
					
					$index = 0;
					$best_index = -1;	
					$sets = array();
					foreach($exercise_sets['sets'] as $set) {
						$new_set = array();
						$new_set['weight'] = $set['weight'];
						$new_set['repetition'] = $set['repetition'];
						if($set['weight'] > 0) 
						{
							$new_set['points'] = $set['weight'] * $set['repetition'];
							$points += $new_set['points'];
						}
						else 
						{
							$new_set['points'] = $set['repetition'];
							$points += $new_set['points'];
						}
						$new_set['calories'] = $set['calories'];
						$calories += $set['calories'];
						$new_set['personalRecord'] = false;
						//echo 'new_set weight: '.$new_set['weight'].', rep:'.$new_set['repetition'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						
						//TODO: Not very accurate
						if($new_set['weight'] >= $best_weight || $new_set['repetition'] >= $best_repetition) 
						{
							$best_weight = $new_set['weight'];
							$best_repetition = $new_set['repetition'];
							$best_index = $index;
							//echo 'best_weight = '.$best_weight.'<br>';
						}
						$index++;
						array_push($sets, $new_set);
					}
					//echo 'post best_weight = '.$best_weight.'<br>';
					$sets[$best_index]['points'] += 50;
					$sets[$best_index]['personalRecord'] = true;
					
					if($best_index != -1) $points += 50;
					
					$user_workout_id = $db->create_workout($userId, 1, $exerciseId, $performed);
					//echo 'user_workout_id: '.$user_workout_id.'<br>';
					/*
					$data = array();
					$data['result'] = array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'1', 'exercise_id'=>$exerciseId);
					//*/
					$setDetails = array();
					foreach($sets as $set) {
						//echo 'weight: '.$set['weight'].', rep:'.$set['repetition'].', calories: '. $set['calories'].', points:'.$set['points'].', personalRecord:'.$set['personalRecord'].'<br>';
						$newSetId = $db->create_conditioning_exercise_sets($user_workout_id, $exerciseId, $set['weight'], $set['repetition'], $set['calories'], $set['points'], $set['personalRecord']);
						$setDetails[] = array('set_id'=>$newSetId, 'weight'=>$set['weight'], 'repetition'=>$set['repetition'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personal_record'=>$set['personalRecord']);
						//$data['result'] = array('set_id'=>$newSetId, 'weight'=>$set['weight'], 'repetition'=>$set['repetition'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personal_record'=>$set['personalRecord']);
					}
					//*
					$data = array();
					$data['result'] = array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'1', 'exercise_id'=>$exerciseId, 'sets'=>$setDetails);
					//*/
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $user_workout_id);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_endurance_exercise_sets')
			{
				//3: logged a workout
				$activityType = 3;
				if(isset($query['userId']) && isset($query['performed']) && isset($query['exerciseId']) && isset($query['sets']) && $query['userId'] && $query['performed'] && $query['exerciseId'] && $query['sets'])
				{
					$points = 0;
					$userId = $query['userId'];
					$performed = $query['performed'];
					$exerciseId = $query['exerciseId'];
					$encoded_string = $query['sets'];
					$json_string = html_entity_decode($encoded_string);
					//echo 'decoded string:'.$json_string.'<br>';
					$exercise_sets = json_decode($json_string, true);
					
					$best_calories = 0;
					$best_distance = 0;
					$best_duration = 0;
					$best_performance = $db->select_endurance_exercises_bytypeId_orderbyperformance($exerciseId, $userId);
					if(!empty($best_performance)) {
						$best_calories = $best_performance['calories']; 
					}
					
					$index = 0;
					$best_index = -1;	
					$sets = array();
					foreach($exercise_sets['sets'] as $set) {
						$new_set = array();
						$new_set['distance'] = $set['distance'];
						$new_set['duration'] = $set['duration'];
						$new_set['points'] = $set['calories'];
						$new_set['calories'] = $set['calories'];
						$new_set['personalRecord'] = false;
						//echo 'new_set distance: '.$new_set['distance'].', duration:'.$new_set['duration'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						
						//TODO: Not very accurate
						if($new_set['calories'] > $best_calories) 
						{
							$best_calories = $new_set['calories'];
							$best_index = $index;
							//echo 'best_calories = '.$best_calories.'<br>';
						}
						$index++;
						array_push($sets, $new_set);
					}
					//echo 'post best_weight = '.$best_weight.'<br>';
					$sets[$best_index]['points'] += 50;
					$sets[$best_index]['personalRecord'] = true;
					
					$user_workout_id = $db->create_workout($userId, 2, $exerciseId, $performed);
					//echo 'user_workout_id: '.$user_workout_id.'<br>';
					
					$setDetails = array();
					foreach($sets as $set) {
						//echo 'distance: '.$new_set['distance'].', duration:'.$new_set['duration'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						$newSetId = $db->create_endurance_exercise_sets($user_workout_id, $exerciseId, $set['duration'], $set['distance'], $set['calories'], $set['points'], $set['personalRecord']);
						$setDetails[] = array('set_id'=>$newSetId, 'duration'=>$set['duration'], 'distance'=>$set['distance'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personalRecord'=>$set['personalRecord']);
					}
					$data = array();
					$data['result'] =  array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'2', 'exercise_id'=>$exerciseId, 'sets'=>$setDetails);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $user_workout_id);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
				
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_sports_exercise_sets')
			{
				//3: logged a workout
				$activityType = 3;
				if(isset($query['userId']) && isset($query['performed']) && isset($query['exerciseId']) && isset($query['sets']) && $query['userId'] && $query['performed'] && $query['exerciseId'] && $query['sets'])
				{
					$exerciseId = 0;
					$userId = $query['userId'];
					$performed = $query['performed'];
					$exerciseId = $query['exerciseId'];
					$encoded_string = $query['sets'];
					$json_string = html_entity_decode($encoded_string);
					//echo 'decoded string:'.$json_string.'<br>';
					$exercise_sets = json_decode($json_string, true);
					
					$best_calories = 0;
					$best_performance = $db->select_sports_exercises_bytypeId_orderbyperformance($exerciseId, $userId);
					if(!empty($best_performance)) 
					{
						$best_calories = $best_performance['calories'];
					}
					
					$index = 0;
					$best_index = -1;	
					$sets = array();
					foreach($exercise_sets['sets'] as $set) {
						$new_set = array();
						$new_set['duration'] = $set['duration'];
						$new_set['intensityId'] = $set['intensityId'];
						$new_set['points'] = $set['calories'];
						$new_set['calories'] = $set['calories'];
						$new_set['personalRecord'] = false;
						//echo 'new_set duration: '.$new_set['duration'].', intensityId:'.$new_set['intensityId'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						
						//TODO: Not very accurate
						if($new_set['calories'] >= $best_calories) 
						{
							$best_calories = $new_set['calories'];
							$best_index = $index;
							//echo 'best_calories = '.$best_calories.'<br>';
						}
						$index++;
						array_push($sets, $new_set);
					}
					//echo 'post best_calories = '.$best_calories.'<br>';
					$sets[$best_index]['points'] += 50;
					$sets[$best_index]['personalRecord'] = true;
					
					$user_workout_id = $db->create_workout($userId, 3, $exerciseId,  $performed);
					//echo 'user_workout_id: '.$user_workout_id.'<br>';
					
					$setDetails = array();
					foreach($sets as $set) {
						//echo 'duration: '.$new_set['duration'].', intensityId:'.$new_set['intensityId'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						$newSetId = $db->create_sports_exercise_sets($user_workout_id, $exerciseId, $set['duration'], $set['intensityId'], $set['calories'], $set['points'], $set['personalRecord']);
						$setDetails[] = array('set_id'=>$newSetId, 'duration'=>$set['duration'], 'intensityId'=>$set['intensityId'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personalRecord'=>$set['personalRecord']);
					}
					$data = array();
					$data['result'] = array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'3', 'exercise_id'=>$exerciseId, 'sets'=>$setDetails);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $user_workout_id);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_class_exercise_sets')
			{
				//3: logged a workout
				$activityType = 3;
				if(isset($query['userId']) && isset($query['performed']) && isset($query['exerciseId']) && isset($query['sets']) && $query['userId'] && $query['performed'] && $query['exerciseId'] && $query['sets'])
				{
					$exerciseId = 0;
					$userId = $query['userId'];
					$performed = $query['performed'];
					$exerciseId = $query['exerciseId'];
					$encoded_string = $query['sets'];
					$json_string = html_entity_decode($encoded_string);
					//echo 'decoded string:'.$json_string.'<br>';
					$exercise_sets = json_decode($json_string, true);
					
					$best_calories = 0;
					$best_performance = $db->select_class_exercises_bytypeId_orderbyperformance($exerciseId, $userId);
					if(!empty($best_performance)) 
					{
						$best_calories = $best_performance['calories'];
					}
					
					$index = 0;
					$best_index = -1;	
					$sets = array();
					foreach($exercise_sets['sets'] as $set) {
						$new_set = array();
						$new_set['duration'] = $set['duration'];
						$new_set['intensityId'] = $set['intensityId'];
						$new_set['points'] = $set['calories'];
						$new_set['calories'] = $set['calories'];
						$new_set['personalRecord'] = false;
						//echo 'new_set duration: '.$new_set['duration'].', intensityId:'.$new_set['intensityId'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						
						//TODO: Not very accurate
						if($new_set['calories'] >= $best_calories) 
						{
							$best_calories = $new_set['calories'];
							$best_index = $index;
							//echo 'best_calories = '.$best_calories.'<br>';
						}
						$index++;
						array_push($sets, $new_set);
					}
					//echo 'post best_calories = '.$best_calories.'<br>';
					$sets[$best_index]['points'] += 50;
					$sets[$best_index]['personalRecord'] = true;
					
					$user_workout_id = $db->create_workout($userId, 4, $exerciseId, $performed);
					//echo 'user_workout_id: '.$user_workout_id.'<br>';
					
					$setDetails = array();
					foreach($sets as $set) {
						//echo 'duration: '.$new_set['duration'].', intensityId:'.$new_set['intensityId'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						$newSetId = $db->create_class_exercise_sets($user_workout_id, $exerciseId, $set['duration'], $set['intensityId'], $set['calories'], $set['points'], $set['personalRecord']);
						$setDetails[] = array('set_id'=>$newSetId, 'duration'=>$set['duration'], 'intensityId'=>$set['intensityId'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personalRecord'=>$set['personalRecord']);
					}
					$data = array();
					$data['result'] = array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'4', 'exercise_id'=>$exerciseId, 'sets'=>$setDetails);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $user_workout_id);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}				
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_stretching_exercise_sets')
			{
				//3: logged a workout
				$activityType = 3;
				if(isset($query['userId']) && isset($query['performed']) && isset($query['exerciseId']) && isset($query['sets']) && $query['userId'] && $query['performed'] && $query['exerciseId'] && $query['sets'])
				{
					$exerciseId = 0;
					$userId = $query['userId'];
					$performed = $query['performed'];
					$exerciseId = $query['exerciseId'];
					$encoded_string = $query['sets'];
					$json_string = html_entity_decode($encoded_string);
					//echo 'decoded string:'.$json_string.'<br>';
					$exercise_sets = json_decode($json_string, true);
					
					$best_calories = 0;
					$best_performance = $db->select_stretching_exercises_bytypeId_orderbyperformance($exerciseId, $userId);
					if(!empty($best_performance)) 
					{
						$best_calories = $best_performance['calories'];
					}
					
					$index = 0;
					$best_index = -1;	
					$sets = array();
					foreach($exercise_sets['sets'] as $set) {
						$new_set = array();
						$new_set['duration'] = $set['duration'];
						$new_set['points'] = $set['calories'];
						$new_set['calories'] = $set['calories'];
						$new_set['personalRecord'] = false;
						//echo 'new_set duration: '.$new_set['duration'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						
						//TODO: Not very accurate
						if($new_set['calories'] >= $best_calories) 
						{
							$best_calories = $new_set['calories'];
							$best_index = $index;
							//echo 'best_calories = '.$best_calories.'<br>';
						}
						$index++;
						array_push($sets, $new_set);
					}
					//echo 'post best_calories = '.$best_calories.'<br>';
					$sets[$best_index]['points'] += 50;
					$sets[$best_index]['personalRecord'] = true;
					
					$user_workout_id = $db->create_workout($userId, 5, $exerciseId, $performed);
					//echo 'user_workout_id: '.$user_workout_id.'<br>';
					
					$setDetails = array();
					foreach($sets as $set) {
						//echo 'duration: '.$new_set['duration'].', intensityId:'.$new_set['intensityId'].', calories: '. $new_set['calories'].', points: '.$new_set['points'].', personal record: '.$new_set['personalRecord'].'<br>';
						$newSetId = $db->create_stretching_exercise_sets($user_workout_id, $exerciseId, $set['duration'], $set['calories'], $set['points'], $set['personalRecord']);
						$setDetails[] = array('set_id'=>$newSetId, 'duration'=>$set['duration'], 'calories'=>$set['calories'], 'points'=>$set['points'], 'personalRecord'=>$set['personalRecord']);
					}
					$data = array();
					$data['result'] = array('workout_id'=>$user_workout_id, 'performed'=>$performed, 'exercise_type_id'=>'5', 'exercise_id'=>$exerciseId, 'sets'=>$setDetails);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $user_workout_id);
					$this->encode_response($query['format'], $data);
					/*
					//*/
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			
			// select workout by userid and date 
			else if (isset($query['operation']) && $query['operation'] == 'select_workout_by_useriddate')
			{
				if(isset($query['userId']) && isset($query['dateWorkedout']) && $query['userId'] && $query['dateWorkedout'])
				{
					$data = array();
					$data['result'] = $db->select_workout_byuseriddate($query['userId'], $query['dateWorkedout']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_workout_byid')
			{
				if(isset($query['workoutId']) && $query['workoutId'])
				{
					$data = array();
					$data['result'] = $db->select_workout_byid($query['workoutId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_workout_byuserid_daterange')
			{
				if(isset($query['userId']) && isset($query['dateWorkedoutStart']) && isset($query['dateWorkedoutEnd']) && $query['userId'] && $query['dateWorkedoutStart'] && $query['dateWorkedoutEnd'])
				{
					$data = array();
					$data['result'] = $db->select_workout_byuserid_daterange($query['userId'], $query['dateWorkedoutStart'], $query['dateWorkedoutEnd']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, performed, exerciseId, sets'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				} 
			}
			/*
			if (isset($query['operation']) && $query['operation'] == 'update_conditioning_exercise_sets')
			{
				if(isset($query['setId']) && isset($query['weight']) && isset($query['repetition']))
				{
					$setId = $query['setId'];
					$weight = $query['weight'];
					$repetition = $query['repetition'];
					// calculate points from calories and check for personal record
					//$calories, $points, $personalRecord
					$data['result'] = $db->update_conditioning_exercise_sets($setId, $weight, $repetition, $calories, $points, $personalRecord);
				}
				else
				{
					die('Missing attributes');
				}
				
			}

			if (isset($query['operation']) && $query['operation'] == 'update_endurance_exercise_sets')
			{
				if(isset($query['setId']) && isset($query['duration']) && isset($query['distance']))
				{
					$setId = $query['setId'];
					$duration = $query['duration'];
					$distance = $query['distance'];
					// calculate points from calories and check for personal record
					// $calories, $points, $personal_record
					$data['result'] = $db->update_endurance_exercise_sets($setId, $duration, $distance, $calories, $points, $personalRecord);
				}
				else
				{
					die('Missing attributes');
				}
				
			}

			if (isset($query['operation']) && $query['operation'] == 'update_sports_exercise_sets')
			{
				if(isset($query['setId']) && isset($query['duration']) && isset($query['intensityId']))
				{
					$setId = $query['setId'];
					$duration = $query['duration'];
					$intensityId = $query['intensityId'];
					// calculate points from calories and check for personal record
					// $calories, $points, $personal_record
					$data['result'] = $db->update_sports_exercise_sets($setId, $duration, $intensityId, $calories, $points, $personalRecord);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			if (isset($query['operation']) && $query['operation'] == 'update_class_exercise_sets')
			{
				if(isset($query['setId']) && isset($query['duration']) && isset($query['intensityId']))
				{
					$setId = 0;
					$duration = $query['duration'];
					$intensity_id = $query['intensityId'];
					// calculate points from calories and check for personal record
					//$calories, $points, $personal_record
					$data['result'] = $db->update_class_exercise_sets($setId, $duration, $intensityId, $calories, $points, $personalRecord);
				}
				else
				{
					die('Missing attributes');
				}				
			}
			
			if (isset($query['operation']) && $query['operation'] == 'update_stretching_exercise_sets')
			{
				if(isset($query['setId']) && isset($query['duration']))
				{
					$setId = 0;
					$duration = $query['duration'];
					// calculate points from calories and check for personal record
					// $calories, $points, $personal_record
					$data['result'] = $db->update_stretching_exercise_sets($setId, $duration, $calories, $points, $personalRecord);
				}
				else
				{
					die('Missing attributes');
				}
			} //*/
			
			// delete exercises by exerciseId 
			else if (isset($query['operation']) && $query['operation'] == 'delete_workout')
			{
				// 5,"deleted a workout"
				$activityType = 5; 
				if(isset($query['workoutId']) && $query['workoutId'] && isset($query['exerciseTypeId']) && $query['exerciseTypeId']) 
				{
					if($db->delete_exercise_byid($query['workoutId'], $query['exerciseTypeId']) > 0) {
						//echo 'workoutId = '.$query['workoutId'].', exerciseTypeId = '.$query['exerciseTypeId'];
						$data['success'] = $query['workoutId'];
						$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['workoutId']);
						$this->encode_response($query['format'], $data); 
					}
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: workoutId'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: workoutId');
				} 
			}
			/*
			// delete conditioning exercises by exerciseId 
			if (isset($query['operation']) && $query['operation'] == 'delete_conditioning_exercises_by_exerciseid')
			{
				if(isset($query['exerciseId']))
				{
					$data['result'] = $db->delete_conditioning_exercises_byexerciseid($query['exerciseId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete conditioning exercises by setId 
			if (isset($query['operation']) && $query['operation'] == 'delete_conditioning_exercise_set_bysetid')
			{
				if(isset($query['setId']))
				{
					$data['result'] = $db->delete_conditioning_exercise_set_bysetid($query['setId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete endurance exercises by exerciseId 
			if (isset($query['operation']) && $query['operation'] == 'delete_endurance_exercises_by_exerciseid')
			{
				if(isset($query['exerciseId']))
				{
					$data['result'] = $db->delete_endurance_exercises_byexerciseid($query['exerciseId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete endurance exercises by setId 
			if (isset($query['operation']) && $query['operation'] == 'delete_endurance_exercises_by_setid')
			{
				if(isset($query['setId']))
				{
					$data['result'] = $db->delete_endurance_exercise_set_bysetid($query['setId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete sports exercises by exerciseId 
			if (isset($query['operation']) && $query['operation'] == 'delete_sports_exercises_by_exerciseid')
			{
				if(isset($query['exerciseId']))
				{
					$data['result'] = $db->delete_sports_exercises_byexerciseid($query['exerciseId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete sports exercises by setId 
			if (isset($query['operation']) && $query['operation'] == 'delete_sports_exercises_by_setid')
			{
				if(isset($query['setId']))
				{
					$data['result'] = $db->delete_sports_exercise_set_bysetid($query['setId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete class exercises by exerciseId 
			if (isset($query['operation']) && $query['operation'] == 'delete_class_exercises_by_exerciseid')
			{
				if(isset($query['exerciseId']))
				{
					$data['result'] = $db->delete_class_exercises_byexerciseid($query['exerciseId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete class exercises by setId 
			if (isset($query['operation']) && $query['operation'] == 'delete_class_exercises_by_setid')
			{
				if(isset($query['setId']))
				{
					$data['result'] = $db->delete_class_exercise_set_bysetid($query['setId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete stretching exercises by exerciseId 
			if (isset($query['operation']) && $query['operation'] == 'delete_stretching_exercises_by_exerciseid')
			{
				if(isset($query['exerciseId']))
				{
					$data['result'] = $db->delete_stretching_exercises_byexerciseid($query['exerciseId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			
			// delete stretching exercises by setId 
			if (isset($query['operation']) && $query['operation'] == 'delete_stretching_exercises_by_setid')
			{
				if(isset($query['setId']))
				{
					$data['result'] = $db->delete_stretching_exercise_set_byexerciseid($query['setId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			//*/
			
			// Food related tasks 
			// create meal 
			else if (isset($query['operation']) && $query['operation'] == 'create_meal')
			{
				// 6: logged a meal
				$activityType = 6;
				if(isset($query['userId']) && isset($query['foodId']) && isset($query['mealId']) && isset($query['servings']) && isset($query['calories']) && isset($query['consumed'])&& $query['userId'] && $query['foodId'] && $query['mealId'] && $query['servings'] && $query['calories'] && $query['consumed'])
				{
					$data['result'] = $db->create_usermeal($query['userId'], $query['foodId'], $query['mealId'], $query['servings'], $query['calories'], $query['consumed']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['result']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			/*
			// select meal by userid 
			if (isset($query['operation']) && $query['operation'] == 'select_meals_by_userid')
			{	
				if(isset($query['userId'])
				{
					$data['result'] = $db->select_usermeals_byuserid($query['userId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			//*/
			// select meals by userid and date 
			else if (isset($query['operation']) && $query['operation'] == 'select_meals_by_userid_date')
			{
				// 25,"viewed an meal log"
				$activityType = 25;
				if(isset($query['requestorId']) && isset($query['userId']) && isset($query['date']) && $query['requestorId'] && $query['userId'] && $query['date'])
				{
					$isEditable = false;
					if($query['requestorId'] == $query['userId']) {
						$isEditable = true;
					}
					$data['result'] = $db->select_usermeals_byuseriddate($query['userId'], $query['date'], $isEditable);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['date']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// select meal by mealid 
			else if (isset($query['operation']) && $query['operation'] == 'select_usermeal_byusermealid')
			{
				// 25,"viewed an meal log"
				$activityType = 25;
				if(isset($query['userMealId']) && $query['userMealId'] && isset($query['userId']) && $query['userId'])
				{
					$data['result'] = $db->select_usermeal_byusermealid($query['userMealId']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userMealId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: userMealId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// update meal
			else if (isset($query['operation']) && $query['operation'] == 'update_meal')
			{
				// 7,"edited a meal"
				$activityType = 7;
				if(isset($query['userId']) && isset($query['userMealId']) && isset($query['servings']) && isset($query['calories']) && $query['userId'] && $query['userMealId'] && $query['servings'] && $query['calories'])
				{
					if($db->update_usermeal($query['userMealId'], $query['servings'], $query['calories']) > 0) {
						$data['success'] = $query['userMealId'];
						$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userMealId']);
						$this->encode_response($query['format'], $data);
					}
					
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, userMealId, servings, points, calories'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: userId, userMealId, servings, points, calories');
				}
			}
			// delete meal by userMealId 
			else if (isset($query['operation']) && $query['operation'] == 'delete_meal')
			{	
				// 8,"deleted a meal
				$activityType = 8;
				if(isset($query['userMealId']) && isset($query['userId']) && $query['userMealId'] && $query['userId'])
				{
					if($db->delete_usermeal($query['userMealId']) > 0) {
						$data['success'] = $query['userMealId'];
						$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['userMealId']);
						$this->encode_response($query['format'], $data);
					}
					
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, userMealId'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: userId, userMealId');
				}
			}
			
			// select food and exercise data
			else if (isset($query['operation']) && $query['operation'] == 'select_all_foodcategories')
			{
				$data['result'] = $db->select_all_foodcategories();
				$this->encode_response($query['format'], $data);
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_food_bycategory')
			{
				if(isset($query['categoryId']) && $query['categoryId'])
				{
					$data['result'] = $db->select_food_bycategory($query['categoryId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_exercises')
			{
				$data['result'] = $db->select_all_exercises();
				$this->encode_response($query['format'], $data);
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_conditioning_exercises')
			{
				$data['result'] = $db->select_all_conditioning_exercises();
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_conditioning_exercise_byid')
			{
				if(isset($query['exerciseId']) && $query['exerciseId']) {
					$data['result'] = $db->select_conditioning_exercise_byid($query['exerciseId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_endurance_exercises')
			{
				$data['result'] = $db->select_all_endurance_exercises();
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_endurance_exercise_byid')
			{
				if(isset($query['exerciseId']) && $query['exerciseId']) {
					$data['result'] = $db->select_endurance_exercise_byid($query['exerciseId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}				
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_sports_exercises')
			{
				$data['result'] = $db->select_all_sports_exercises();
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_sports_exercise_byid')
			{
				if(isset($query['exerciseId']) && $query['exerciseId']) {
					$data['result'] = $db->select_sports_exercise_byid($query['exerciseId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}			
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_class_exercises')
			{
				$data['result'] = $db->select_all_class_exercises();
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_class_exercise_byid')
			{
				if(isset($query['exerciseId']) && $query['exerciseId']) {
					$data['result'] = $db->select_class_exercise_byid($query['exerciseId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_all_stretching_exercises')
			{
				$data['result'] = $db->select_all_stretching_exercises();
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_stretching_exercise_byid')
			{
				if(isset($query['exerciseId']) && $query['exerciseId']) {
					$data['result'] = $db->select_stretching_exercise_byid($query['exerciseId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// Feeds related tasks
			else if (isset($query['operation']) && $query['operation'] == 'select_userfeeds_byrange')
			{
				if(isset($query['userId']) && $query['userId'])
				{
					$data['result'] = $db->select_userfeeds_byrange($query['userId']);
					/*
					if(isset($query['offset']) && $query['offset']) 
					{
						$data['result'] = $db->select_userfeeds_byrange($userId, $offset);
					}
					else 
					{
						$data['result'] = $db->select_userfeeds_byrange($userId);
					}//*/
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_personalfeeds_byrange')
			{
				if(isset($query['userId']) && isset($query['requestorId']) && $query['userId'] && $query['requestorId'])
				{
					$data['result'] = $db->select_personalfeeds_byrange($query['userId'], $query['requestorId']);
					/*
					if(isset($query['offset']) && $query['offset']) 
					{
						$data['result'] = $db->select_userfeeds_byrange($userId, $offset);
					}
					else 
					{
						$data['result'] = $db->select_userfeeds_byrange($userId);
					}//*/
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			
			// Notes related tasks
			// create note
			else if (isset($query['operation']) && $query['operation'] == 'create_note')
			{
				if(isset($query['userId']) && isset($query['noteType']) && isset($query['itemId']) && isset($query['content']) && $query['userId'] && $query['noteType'] && $query['itemId'] && $query['content'])
				{
					//13,"posted a status"
					$activityType = 13;
					$data['row_inserted'] = $db->create_note($query['userId'], $query['noteType'], $query['itemId'], $query['content']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['row_inserted']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id, noteType, itemId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// select note by noteid
			else if (isset($query['operation']) && $query['operation'] == 'select_note_bytypeid_itemid')
			{
				if(isset($query['notetypeId']) && isset($query['itemId']) && $query['notetypeId'] && $query['itemId'])
				{
					$data['result'] = $db->select_note_bytypeid_itemid($query['notetypeId'], $query['itemId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: noteTypeId, itemId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: noteTypeId, itemId.');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_note_byid_withsocialstatus')
			{
				if(isset($query['noteId']) && $query['noteId'])
				{
					$data['result'] = $db->select_note_byid_withsocialstatus($query['noteId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: noteTypeId, itemId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: noteTypeId, itemId.');
				}
			}		
			// update note
			else if (isset($query['operation']) && $query['operation'] == 'update_note')
			{
				// update status
				//14,"edited a status",NULL,0
				$activityId = 14;
				if(isset($query['noteId']) && isset($query['content']) && $query['noteId'] && $query['content'])
				{
					$data['result'] = $db->update_note($query['noteId'], $query['content']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['noteId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: noteId, content'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes: noteId, content');
				}
			}
			/*
			// delete note
			 else if (isset($query['operation']) && $query['operation'] == 'delete_note')
			{
				if(isset($query['noteId']))
				{
					$data['result'] = $db->delete_note($query['noteId']);
				}
				else
				{
					die('Missing attributes');
				}
			}
			//*/
			
			// create comment
			else if (isset($query['operation']) && $query['operation'] == 'create_comment')
			{
				if(isset($query['userId']) && isset($query['activityId']) && isset($query['content']) && $query['userId'] && $query['activityId'] && $query['content'])
				{
					$activityType = 15;
					$data['commentId'] = $db->create_comment($query['userId'], $query['activityId'], $query['content']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['commentId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id, noteType, itemId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_comment_article')
			{
				if(isset($query['userId']) && isset($query['activityId']) && isset($query['content']) && $query['userId'] && $query['activityId'] && $query['content'])
				{
					$activityType = 9;
					$data['commentId'] = $db->create_comment($query['userId'], $query['activityId'], $query['content']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['commentId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: user id, noteType, itemId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			// select comments by feedid
			else if (isset($query['operation']) && $query['operation'] == 'select_comments_byfeedid')
			{
				if(isset($query['activityTypeId']) && isset($query['itemId']) && $query['activityTypeId'] && $query['itemId'])
				{
					$data['result'] = $db->select_comments_byfeedid($query['activityTypeId'], $query['itemId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: activityTypeId, itemId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
				
			}
			// update comment
			else if (isset($query['operation']) && $query['operation'] == 'update_comment')
			{
				if(isset($query['commentId']) && isset($query['content']) && $query['commentId'] && $query['content'])
				{
					$activityType = 10;
					$data['result'] = $db->update_comment($query['commentId'], $query['content']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['result']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			/*			
			// delete comment
			else if (isset($query['operation']) && $query['operation'] == 'delete_comment')
			{
				if(isset($query['commentId']))
				{
					$data['result'] = $db->delete_comment($query['commentId']);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			//*/
			
			// Kudos related tasks
			// create kudos
			else if (isset($query['operation']) && $query['operation'] == 'create_kudos_article')
			{
				if(isset($query['activityId']) && isset($query['itemId']) && isset($query['userId']) && $query['activityId'] && $query['itemId'] && $query['userId'])
				{
					$activityType = 11;
					$data['kudosId'] = $db->create_kudos($query['activityId'], $query['itemId'], $query['userId']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['kudosId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'create_kudos_status')
			{
				if(isset($query['activityId']) && isset($query['itemId']) && isset($query['userId']) && $query['activityId'] && $query['itemId'] && $query['userId'])
				{
					$activityType = 17;
					$data['kudosId'] = $db->create_kudos($query['activityId'], $query['itemId'], $query['userId']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $data['kudosId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'select_kudos_byfeedid')
			{
				if(isset($query['activityTypeId']) && isset($query['itemId']) && $query['activityTypeId'] && $query['itemId'])
				{
					$data['result'] = $db->select_kudos_byfeedid($query['activityTypeId'], $query['itemId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: activityTypeId, itemId.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			
			// delete kudos 
			else if (isset($query['operation']) && $query['operation'] == 'delete_kudos_article')
			{
				if(isset($query['kudosId']))
				{
					$activityType = 12;
					if($db->delete_kudos($query['kudosId']) > 0) 
					{
						$data['success'] = $query['kudosId'];
						$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['kudosId']);
						$this->encode_response($query['format'], $data);
					}
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if (isset($query['operation']) && $query['operation'] == 'delete_kudos_status')
			{
				if(isset($query['kudosId']))
				{
					$activityType = 18;
					if($db->delete_kudos($query['kudosId']) > 0)
					{
						$data['success'] = $query['kudosId'];
						$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['kudosId']);
						$this->encode_response($query['format'], $data);
					}
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attribute: commentId, content.'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			/*
			//*/
			
			else if(isset($query['operation']) && $query['operation'] == 'select_articles_byrange')
			{
				if(isset($query['range']) && $query['range'])
				{
					$data['result'] = $db->select_articles_byrange($query['range']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: range'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			else if(isset($query['operation']) && $query['operation'] == 'select_article_byid')
			{
				// 23: viewed an article
				if(isset($query['articleId']) && $query['articleId'])
				{
					$activityType = 23;
					$data['result'] = $db->select_article_byid($query['articleId']);
					$log['activityId'] = $db->create_activity($query['userId'], $activityType, $query['articleId']);
					$this->encode_response($query['format'], $data);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: articleId'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			/*
			else if(isset($query['operation']) && $query['operation'] == 'create_multimedia')
			{
				if(isset($query['userId']) && isset($query['itemType']) && isset($query['itemId']) && isset($query['itemPath']) && isset($query['itemFileType']) && isset($query['itemSize']) && $query['userId']) && $query['itemType'] && $query['itemId'] && $query['itemPath'] && $query['itemFileType'] && $query['itemSize'])
				{
					$data['result'] = $db->create_multimedia($query['userId']) && $query['itemType'] && $query['itemId'] && $query['itemPath'] && $query['itemFileType'] && $query['itemSize']);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: userId, itemType, itemId, itemPath, itemFileType, itemSize'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}
			//*/
			/* template
			else if(isset($query['operation']) && $query['operation'] == '')
			{
				if(isset($query['']) && $query[''])
				{
					$data['result'] = $db->delete_kudos($query['kudosId']);
				}
				else
				{
					$showStopperError = true;
					$code = 43; // Missing attributes
					$title = 'User error.'; // summary of problem
					$detail = 'Missing attributes: firstname, lastname, email, password, sex, height, weight'; // explanation of problem
					$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
					die('Missing attributes');
				}
			}//*/
			else
			{
				$showStopperError = true;
				$code = 42; // Invalid operation
				$title = 'User error.'; // summary of problem
				$detail = 'Invalid operation: '.$query['operation'].'.'; // explanation of problem
				$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
				die('Invalid operation: '.$query['operation'].'.<br>');
			}		
		} //if(sizeof($_POST) > 0)
		else
		{
			$showStopperError = true;
			$code = 44; // Missing parameters
			$title = 'User error.'; // summary of problem
			$detail = 'Missing parameters.'; // explanation of problem
			$errorMessages[] = array('id'=>$id++, 'status'=>$status, 'code'=>$code, 'title'=>$title, 'detail'=>$detail);
			die('Missing parameters.<br>');
		}
		
		if($showStopperError)
		{
			$errorResponse = array();
			$errorResponse[] = array('errors'=>$errorMessages);
			print json_encode($errorResponse);
		}
	}
	
	public function encode_response($format, $data)
	{
		if($format == 'json')
		{
			print json_encode($data);
		}
		else if($format == 'xml')
		{
			die('Oops! It seems that xml is not being supported yet.');
		}
		else 
		{
			die('Hmm...'.$format.' format is not recognized.');
		}
	}
}

?>
