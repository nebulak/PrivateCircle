<?php

require_once './classes/ErrorMessage.php';
require_once 'config.php';


class UserTool
{
	public function registerUser($username, $password, $invite_code, $salt, $public_key, $private_key)
	{
		//Check if username already exists
		$user  = R::findOne( 'user', '  username = ? ', [ strtolower($username)]);

		if($user != NULL )
		{
			$error = new ErrorMessage("The username already exists!");
			return json_encode($error);
		}

		//check if first user is registered
		if( count(R::findAll( 'user' )) != 0 )
		{
			//check if given invite-code exists
			$invitor = R::findOne( 'user', '  invite_code = ? ', [ strtolower($invite_code)]);

			if( $invitor == NULL )
			{
				$error = new ErrorMessage("The invite-code is wrong!");
				return json_encode($error);
			}
		}

		//create new user
		$user = R::dispense( 'user' );
		$user->username = strtolower($username);
		$user->salt = $salt;
		$user->password = hash('sha512', $password . $salt);
		$user->invite_code = $this->random_string(32);
		$user->invited_with_code = $invite_code;
		$user->public_key = $public_key;
		$user->private_key = $private_key;

		$id = R::store( $user );

		$data = array("rc" => 0);
		return json_encode($data);
	}


	public function loginUser($username, $password)
	{
		//Check if username already exists
		$user  = R::findOne( 'user', '  username = ? ', [ strtolower($username) ]);
		if($user == NULL )
		{
			$error = new ErrorMessage("The username or the password is wrong!");
			return json_encode($error);
		}

		if(hash('sha512', $password . $user->salt) != $user->password)
		{
			$error = new ErrorMessage("The username or the password is wrong!");
			return json_encode($error);
		}

		//createSession
		$session_key = $this->random_string(128);
		$session = R::findOne( 'session', '  id = ? ', [ $user->id]);

		if($session == NULL)
		{
			$session = R::dispense( 'session' );
			$session->token = $session_key;
			$session->user_id = $user->id;
			$id = R::store( $session );

			$data = array(	"token" => $session_key, 
							"public_key" => $user->public_key,
							"private_key" => $user->private_key
						 );
			return json_encode($data);
		}
		else
		{
			$session->token = $session_key;
			$session->user_id = $user->id;
			$id = R::store( $session );

			$data = array(	"token" => $session_key, 
							"public_key" => $user->public_key,
							"private_key" => $user->private_key
						 );
			return json_encode($data);
		}

	}


	public function getUserByToken($token)
	{
		$session = R::findOne( 'session', '  token = ? ', [ $token]);
		if($session == NULL)
		{
			return NULL;
		}

		$user = R::load( 'user', $session->user_id );
		if($user == NULL)
		{
			return NULL;
		}

		return $user;
	}

	public function getUsernames()
	{
		$users = R::findAll( 'user' );

		$usernames = array();
		foreach($users as $user)
		{
			array_push($usernames, $user->username);
		}
		return json_encode($usernames);
	}



	public function getDashboard($user_id)
	{
		$user = R::findOne( 'user', '  id = ? ', [ $user_id]);
		$messages = R::find( 'message', '   recipient_id = ? ', [$user_id]);
		if($user != NULL)
		{
			$data = array("username" => $user->username, "msg_num" => count($messages), "invite_code" => $user->invite_code);
			return json_encode($data);
		}
	}



	public function getNewPasswordSalt()
	{
		//create salt for user password
		$salt = $this->random_string(32);
		$data = array("salt" => $salt);
		return json_encode($data);
	}

	public function getSaltForUser($username)
	{
		$user = R::findOne( 'user', '  username = ? ', [ strtolower($username)]);
		if($user != NULL)
		{
			$data = array("salt" => $user->salt);
			return json_encode($data);
		}
	}

	private function random_string( $length ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	
		$str = "";
		$size = strlen( $chars );
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}

		return $str;
	}
}




?>