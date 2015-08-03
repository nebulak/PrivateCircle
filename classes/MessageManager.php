<?php
require_once './classes/ErrorMessage.php';
require_once 'config.php';


class MessageManager
{
	
	public function sendMessage($sender_id, $recipient, $content)
	{

		$recipient_id = $this->getRecipientId($recipient);
		if($recipient_id == NULL)
		{
			$error = new ErrorMessage("The recipient does not exist!");
			return json_encode($error);
		}

		//create message bean with message
		$msg = R::dispense( 'message' );
		$msg->sender_id = $sender_id;
		$msg->recipient_id = $recipient_id;
		$msg->content = $content;
		$now = new DateTime();
		$msg->date = $now->format('d-m-Y H:i'); 

		$id = R::store( $msg );

		$data = array("rc" => 0);
		return json_encode($data);
	}


	public function getRecipientId($recipient_name)
	{
		$recipient = R::findOne( 'user', '  username = ? ', [ strtolower($recipient_name)]);
		if($recipient == NULL)
		{
			return NULL;
		}

		return $recipient->id;
	}

	public function getPublicKeyForUser($username)
	{
		$user = R::findOne( 'user', '  username = ? ', [ strtolower($username)]);
		if($user == NULL)
		{
			return NULL;
		}

		return json_encode(array("public_key" => $user->public_key));
	}


	public function getMessagesForUser($user_id)
	{
		$messageList = R::find( 'message',  '  recipient_id = ? ', [ $user_id], ' ORDER BY date ASC ');

		$messages = array();
		foreach($messageList as $message)
		{
			$sender = R::findOne( 'user', '  id = ? ', [ $message->sender_id]);
			$currentElement = array("sender" => $sender->username, "content" => $message->content, "date" => $message->date, "m_id" => $message->id);

			array_push($messages, $currentElement);
		}
		return json_encode($messages);

	}


	public function deleteMessage($user_id, $m_id)
	{
		$message = R::findOne( 'message', '  recipient_id = ? && id = ? ', [ $user_id, $m_id]);
		if($message == NULL)
		{
			return NULL;
		}
		R::trash( $message );
		$data = array("rc" => 0);
		return json_encode($data);
	}
}


?>