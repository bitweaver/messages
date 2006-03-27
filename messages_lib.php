<?php
/**
* message package modules
*
* @author   
* @version  $Revision: 1.7 $
* @package  messages
*/

/**
* Messages base class
*
* @package  messages
* @subpackage  Messages
*/
class Messages extends BitBase {

	function Messages() {
		BitBase::BitBase();
	}

	function post_message( $pToLogin, $to, $cc, $bcc, $subject, $body, $priority, $group_id = NULL) {
		global $gBitSmarty, $gBitUser, $gBitSystem;
		
		$userInfo = $gBitUser->getUserInfo( array('login' => $pToLogin) );
		if (!$userInfo) {
			if (is_numeric($pToLogin)) {
				$userInfo = $gBitUser->getUserInfo( array('user_id' => $pToLogin) );
			}
		}
		
		if( $userInfo ) {
			if ($gBitUser->getPreference('messages_allow_messages', 'y', $userInfo['user_id'] )) {
				$subject = strip_tags($subject);
				$body = strip_tags($body, '<a><b><img><i>');
				// Prevent duplicates
				$hash = md5($subject . $body);

				if ($this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `from_user_id`=? and `hash`=?", array( $userInfo['user_id'], $gBitUser->mUserId, $hash ) ) ) {
					$this->mErrors['compose'] = $pToLogin.' '.tra( 'has already received this message' );
				} else {
					$bitDate = $gBitSystem->get_date_converter();
					$now = $bitDate->getUTCTime();
					$query = "INSERT INTO `".BIT_DB_PREFIX."messages`
							  (`to_user_id`, `from_user_id`, `msg_to`, `msg_cc`, `msg_bcc`, `subject`, `body`, `msg_date`, `is_read`, `is_replied`, `is_flagged`, `priority`, `hash`, `group_id` )
							  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					$this->mDb->query( $query, array( $userInfo['user_id'], $gBitUser->mUserId, $to, $cc, $bcc, $subject, $body,(int) $now,'n','n','n',(int) $priority,$hash, $group_id ) );

					// Now check if the user should be notified by email
					$foo = parse_url($_SERVER["REQUEST_URI"]);
					$machine = httpPrefix(). $foo["path"];

					if ($gBitUser->getPreference( 'minPrio', 3 ) <= $priority && FALSE) {
						$mailSite = $gBitSystem->getConfig( 'kernel_server_name', $_SERVER["SERVER_NAME"] );
						$gBitSmarty->assign( 'mail_site', $mailSite );
						$gBitSmarty->assign( 'mail_machine', $machine);
						$gBitSmarty->assign( 'mail_date', $now);
						$gBitSmarty->assign( 'mail_user', stripslashes( $userInfo['login'] ) );
						$gBitSmarty->assign( 'mail_from', stripslashes( $gBitUser->getDisplayName() ) );
						$gBitSmarty->assign( 'mail_subject', stripslashes($subject));
						$gBitSmarty->assign( 'mail_body', stripslashes($body));
						$mail_data = $gBitSmarty->fetch('bitpackage:messages/messages_message_notification.tpl');

						if( !empty( $userInfo['email'] ) ) {
							@mail($userInfo['email'], tra('New message arrived from '). $mailSite, $mail_data,
								"From: ".$gBitSystem->getConfig( 'sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n");
						}
					}
				}
			} else {
				// TODO: needs translation as soon as there is a solution for strings with embedded variables
				$this->mErrors['compose'] = $pToLogin.' '.tra( 'can not receive messages' );
			}
		} else {
			$this->mErrors['compose'] .= tra( 'Unknown user' ).": $pToLogin";
		}

		return( count( $this->mErrors ) == 0 );
	}
	
	function post_system_message($subject, $body, $group_id) {
		return $this->post_message(ROOT_USER_ID, ROOT_USER_ID,NULL,NULL,$subject, $body, 1, $group_id);
	}
	
	function list_system_messages() {
		$sql = "SELECT mm.* FROM `".BIT_DB_PREFIX."messages` mm WHERE mm.`from_user_id` = ?";
		$rs = $this->mDb->query($sql, array(ROOT_USER_ID));
		
		return $rs->getRows();
	}
	
	function remove_system_message($pMessageID = NULL) {
		if ($pMessageID) {
			$sql = "DELETE FROM `".BIT_DB_PREFIX."messages_system_map` WHERE msg_id = ?";
			$rs = $this->mDb->query($sql, array($pMessageID));
			
			$sql = "DELETE FROM `".BIT_DB_PREFIX."messages` WHERE msg_id = ?";
			$rs = $this->mDb->query($sql, array($pMessageID));
		}
		
	}
	
	function is_system_message($pMessageID = NULL) {
		$ret = FALSE;
		if ($pMessageID) {
			$query = "SELECT COUNT(msg_id) FROM `".BIT_DB_PREFIX."messages` WHERE `to_user_id` = ? AND `msg_id` = ?";
			$ret = $this->mDb->getOne($query, array(ROOT_USER_ID, $pMessageID));	
		}
		return $ret;
	}
	
	function list_messages( $pUserId, $offset, $max_records, $sort_mode, $find, $flag = '', $flagval = '', $prio = '' ) {
		
		// Load all normal messages (user to user)
		$bindvars = array($pUserId);
		$mid="";
		if ($prio) {
			$mid = " and priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " and `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.strtoupper( $find ).'%';
			$mid.= " and (UPPER(`subject`) like ? or UPPER(`body`) like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "SELECT uu.`login` AS `user`, uu.`real_name`, uu.`user_id`, mm.* from `".BIT_DB_PREFIX."messages` mm INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( mm.`from_user_id`=uu.`user_id` )
				  WHERE `to_user_id`=? $mid
				  ORDER BY ".$this->mDb->convert_sortmode($sort_mode).",".$this->mDb->convert_sortmode("msg_id_desc");
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."messages` where `to_user_id`=? $mid";
		$result = $this->mDb->query($query,$bindvars,$max_records,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$normalMessages = array();

		while ($res = $result->fetchRow()) {
			$res["len"] = strlen($res["body"]);

			if (empty($res['subject']))
				$res['subject'] = tra('NONE');

			$normalMessages[] = $res;
		}
		
		// Load system messages (i.e. broadcast messages) 
		$bindvars = array($pUserId, ROOT_USER_ID, $pUserId);
		$mid="";
		if ($prio) {
			$mid = " and mm.priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " and mm.`$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.strtoupper( $find ).'%';
			$mid.= " and (UPPER(mm.`subject`) like ? or UPPER(mm.`body`) like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
				
		$query = "SELECT uu.`login` AS `user`, uu.`real_name`, uu.`user_id`, mm.`msg_id` as `msg_id_foo`, mm.`msg_to`, mm.`msg_cc`, mm.`msg_bcc`, mm.`subject`, mm.`body`, mm.`hash`, mm.`msg_date`, msm.* 
				  FROM `".BIT_DB_PREFIX."messages` mm  
				  	INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (mm.`from_user_id` = uu.`user_id`) 
				  	LEFT OUTER JOIN `".BIT_DB_PREFIX."messages_system_map` msm  ON (mm.`msg_id` = msm.`msg_id` AND msm.`to_user_id` = ?)
				  WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) $mid 
				  ORDER BY ".$this->mDb->convert_sortmode($sort_mode).",".$this->mDb->convert_sortmode("mm.msg_id_desc");
		
		$query_cant = "SELECT COUNT(mm.`msg_id`) 
		 			   FROM `".BIT_DB_PREFIX."messages` mm 
		 			     LEFT OUTER JOIN `".BIT_DB_PREFIX."messages_system_map` msm ON (mm.`msg_id` = msm.`msg_id` AND msm.`to_user_id` = ?)
					   WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) $mid";
		$result2 = $this->mDb->query($query, $bindvars);
		$cant2 = $this->mDb->getOne($query_cant, $bindvars);
		$systemMessages = array();
		while ($res = $result2->fetchRow()) {
			$res['len'] = strlen($res['body']);
			$res['is_broadcast_message'] = TRUE;
			if (empty($res['subject'])) {
				$res['subject'] = tra('NONE');
			}
			$res['msg_id'] = $res['msg_id_foo'];	// Due to the left outer join this madness is neccessary
			unset($res['msg_id_foo']);
			if ($res['is_hidden'] != 'y') {
				$systemMessages[] = $res;		
			}
		}
		
		// Now we merge normalMessages and systemMessages and put them in order
		$ret = array();
		$normalMessageCount = count($normalMessages);
		$systemMessageCount = count($systemMessages);
		$normalMsg = $systemMsg = NULL;
		if (strpos($sort_mode, '_asc') !== FALSE) {
			$sortType = '_asc';
			$sortKey = substr($sort_mode, 0, strlen($sort_mode)-4);
		} else {
			$sortType = '_desc';
			$sortKey = substr($sort_mode, 0, strlen($sort_mode)-5);
		}
		
		while ($normalMessageCount > 0 || $systemMessageCount > 0) {
			if (!$normalMsg && $normalMessageCount > 0) {
				$normalMsg = array_shift($normalMessages);
			}
			if (!$systemMsg && $systemMessageCount > 0) {
				$systemMsg = array_shift($systemMessages);	
			}
			if ($normalMessageCount == 0) {
				$ret[] = $systemMsg;
				$systemMsg = NULL;
				$systemMessageCount--;
			} elseif ($systemMessageCount == 0) {
				$ret[] = $normalMsg;
				$normalMsg = NULL;
				$normalMessageCount--;
			}elseif ($sortType == '_asc') {
				if ($normalMsg[$sortKey] < $systemMsg[$sortKey]) {
					$ret[] = $normalMsg;
					$normalMsg = NULL;
					$normalMessageCount--;
				} else {
					$ret[] = $systemMsg;
					$systemMsg = NULL;
					$systemMessageCount--;
				}	
			} else {
				if ($normalMsg[$sortKey] > $systemMsg[$sortKey]) {
						$ret[] = $normalMsg;
					$normalMsg = NULL;
					$normalMessageCount--;
				} else {
					$ret[] = $systemMsg;
					$systemMsg = NULL;
					$systemMessageCount--;
				}				
			}
		}
		
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant + $cant2;
		return $retval;
	}

	function flag_message( $pUserId, $msg_id, $flag, $val ) {
		if (!$msg_id)
			return false;
		if ($this->is_system_message($msg_id)) {
			$query = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."messages_system_map` WHERE `to_user_id` = ? AND `msg_id` = ?";
			$rowExists = $this->mDb->getOne($query, array($pUserId, $msg_id));
			if ($rowExists) {
				$query = "UPDATE `".BIT_DB_PREFIX."messages_system_map` SET `$flag`=? WHERE `to_user_id` = ? AND `msg_id` = ?";
				$this->mDb->query($query, array($val, $pUserId, (int)$msg_id));	
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."messages_system_map` (`msg_id`, `to_user_id`, `$flag`) VALUES (?,?,?)";
				$this->mDb->query($query, array((int)$msg_id, $pUserId, $val));
			}
			
		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."messages` SET `$flag`=? where `to_user_id`=? and `msg_id`=?";
			$this->mDb->query($query,array($val,$pUserId,(int)$msg_id));
		}
	}

	function delete_message($pUserId, $msg_id) {
		if (!$msg_id)
			return false;
		if ($this->is_system_message($msg_id)) {
			// We just mark this user's messages_system_map row is_hidden = 'y'
			$query = "UPDATE `".BIT_DB_PREFIX."messages_system_map` SET `is_hidden` = 'y' WHERE `to_user_id` = ? AND `msg_id` = ?";
			$this->mDb->query($query, array($pUserId, $msg_id));	
		} else {
			$query = "delete from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `msg_id`=?";
			$this->mDb->query($query,array($pUserId,(int)$msg_id));
		}
	}

	function get_next_message($pUserId, $msg_id, $sort_mode, $find, $flag, $flagval, $prio) {
		if (!$msg_id)
			return 0;

		$mid = "";
		$bindvars = array($pUserId,(int)$msg_id);
		if ($prio) {
			$mid.= " and priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " and `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.strtoupper( $find ).'%';
			$mid.= " and (UPPER(`subject`) like ? or UPPER(`body`) like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select min(`msg_id`) as `nextmsg` from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `msg_id` > ? $mid ";
		$result = $this->mDb->query($query,$bindvars,1,0);
		$res = $result->fetchRow();

		if (!$res)
			return false;
		return $res['nextmsg'];
	}

	function get_prev_message($pUserId, $msg_id, $sort_mode, $find, $flag, $flagval, $prio) {
		if (!$msg_id)
			return 0;

		$bindvars = array( $pUserId, (int)$msg_id );
		$mid="";
		if ($prio) {
			$mid.= " AND priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " AND `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.strtoupper( $find ).'%';
			$mid.= " and (UPPER(`subject`) like ? or UPPER(`body`) like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		$query = "select max(`msg_id`) as `prevmsg` from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `msg_id` < ? $mid";
		$result = $this->mDb->query( $query, $bindvars, 1, 0 );
		$res = $result->fetchRow();

		if (!$res)
			return false;

		return $res['prevmsg'];
	}

	function get_message( $pUserId, $msg_id ) {
		if (!$this->is_system_message($msg_id)) {
			$bindvars = array( $pUserId, (int)$msg_id );
			$query = "select * from `".BIT_DB_PREFIX."messages` WHERE `to_user_id`=? and `msg_id`=?";
			$result = $this->mDb->query($query,$bindvars);
			$res = $result->fetchRow();		
		} else {
			$bindvars = array($pUserId, (int)$msg_id);
			$query = "SELECT msm.*, ug.`group_name`, mm.`from_user_id`, mm.`msg_id` as `msg_id_foo`, mm.`msg_to`, mm.`msg_cc`, mm.`msg_bcc`, mm.`subject`, mm.`body`, mm.`hash`, mm.`msg_date` 
					  FROM `".BIT_DB_PREFIX."messages` mm 
					    INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON (ug.`group_id` = mm.`group_id`)
					    LEFT OUTER JOIN `".BIT_DB_PREFIX."messages_system_map` msm ON (mm.`msg_id` = msm.`msg_id` AND msm.`to_user_id` = ?)
					  WHERE mm.`msg_id` = ?";
			$result = $this->mDb->query($query, $bindvars);
			$res = $result->fetchRow();
			$res['is_broadcast_message'] = TRUE;
		}
			
		$content = new LibertyContent();
		$res['parsed'] = $content->parseData( $res['body'], PLUGIN_GUID_TIKIWIKI );
	
		if (empty($res['subject']))
			$res['subject'] = tra('NONE');

		return $res;
	}

	/*shared*/
	function user_unread_messages( $pUserId ) {
		// Standard user to user messages
		$normalCount =  $this->mDb->getOne( "select count( * ) from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `is_read`=?",array( $pUserId,'n' ) );
		// Broadcast messages where they have a messages_system_map row but is_read is not yet set
		$broadcastCount = $this->mDb->getOne("SELECT COUNT(mm.`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm INNER JOIN `".BIT_DB_PREFIX."messages_system_map` msm ON (mm.`msg_id` = msm.`msg_id` AND msm.`is_read` <> 'y' AND `is_hidden` <> 'y' AND msm.`to_user_id`= ?) WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id`= ?) ", array($pUserId, ROOT_USER_ID, $pUserId));
		// Broadcast messages where they do not yet have a messages_system_map row
		$broadcastCount2 = $this->mDb->getOne("SELECT COUNT(mm.`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) AND NOT EXISTS ( SELECT msm.`msg_id` FROM `".BIT_DB_PREFIX."messages_system_map` msm WHERE msm.`msg_id` = mm.`msg_id` AND msm.`to_user_id` = ?)", array(ROOT_USER_ID, $pUserId, $pUserId));
		return $normalCount + $broadcastCount + $broadcastCount2;
	}
}

global $messageslib;
$messageslib = new Messages();

?>
