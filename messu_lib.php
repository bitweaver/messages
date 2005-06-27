<?php
/**
* message package modules
*
* @author   
* @version  $Revision: 1.1.1.1.2.1 $
* @package  messages
*/

/**
* Messu base class
*
* @package  messages
* @subpackage  Messu
*/
class Messu extends BitBase {

	function Messu() {
		BitBase::BitBase();
	}

	function post_message( $pToLogin, $to, $cc, $bcc, $subject, $body, $priority) {
		global $smarty, $gBitUser, $gBitSystem;

		$userInfo = $gBitUser->getUserInfo( array('login' => $pToLogin) );
		if( $userInfo ) {
			if ($gBitUser->getPreference('allowMsgs', 'y', $userInfo['user_id'] )) {
				$subject = strip_tags($subject);
				$body = strip_tags($body, '<a><b><img><i>');
				// Prevent duplicates
				$hash = md5($subject . $body);

				if ($this->getOne("select count(*) from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? and `from_user_id`=? and `hash`=?", array( $userInfo['user_id'], $gBitUser->mUserId, $hash ) ) ) {
					$this->mErrors['compose'] = $pToLogin.' '.tra( 'has already received this message' );
				} else {

					$now = date('U');
					$query = "INSERT INTO `".BIT_DB_PREFIX."messu_messages`
							  (`to_user_id`, `from_user_id`, `msg_to`, `msg_cc`, `msg_bcc`, `subject`, `body`, `date`, `is_read`, `is_replied`, `is_flagged`, `priority`, `hash` )
							  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
					$this->query( $query, array( $userInfo['user_id'], $gBitUser->mUserId, $to, $cc, $bcc, $subject, $body,(int) $now,'n','n','n',(int) $priority,$hash ) );

					// Now check if the user should be notified by email
					$foo = parse_url($_SERVER["REQUEST_URI"]);
					$machine = httpPrefix(). $foo["path"];

					if ($gBitUser->getPreference( 'minPrio', 3 ) <= $priority) {
						$mailSite = $gBitSystem->getPreference( 'feature_server_name', $_SERVER["SERVER_NAME"] );
						$smarty->assign( 'mail_site', $mailSite );
						$smarty->assign( 'mail_machine', $machine);
						$smarty->assign( 'mail_date', date("U"));
						$smarty->assign( 'mail_user', stripslashes( $userInfo['login'] ) );
						$smarty->assign( 'mail_from', stripslashes( $gBitUser->getDisplayName() ) );
						$smarty->assign( 'mail_subject', stripslashes($subject));
						$smarty->assign( 'mail_body', stripslashes($body));
						$mail_data = $smarty->fetch('bitpackage:messu/messu_message_notification.tpl');

						if( !empty( $userInfo['email'] ) ) {
							@mail($userInfo['email'], tra('New message arrived from '). $mailSite, $mail_data,
								"From: ".$gBitSystem->getPreference( 'sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n");
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

	function list_messages( $pUserId, $offset, $maxRecords, $sort_mode, $find, $flag = '', $flagval = '', $prio = '' ) {
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

		$query = "SELECT uu.`login` AS `user`, uu.`real_name`, uu.`user_id`, mm.* from `".BIT_DB_PREFIX."messu_messages` mm INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( mm.`from_user_id`=uu.`user_id` )
				  WHERE `to_user_id`=? $mid
				  ORDER BY ".$this->convert_sortmode($sort_mode).",".$this->convert_sortmode("msg_id_desc");
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["len"] = strlen($res["body"]);

			if (empty($res['subject']))
				$res['subject'] = tra('NONE');

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function flag_message( $pUserId, $msg_id, $flag, $val ) {
		if (!$msg_id)
			return false;
		$query = "update `".BIT_DB_PREFIX."messu_messages` set `$flag`=? where `to_user_id`=? and `msg_id`=?";
		$this->query($query,array($val,$pUserId,(int)$msg_id));
	}

	function delete_message($pUserId, $msg_id) {
		if (!$msg_id)
			return false;
		$query = "delete from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? and `msg_id`=?";
		$this->query($query,array($pUserId,(int)$msg_id));
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

		$query = "select min(`msg_id`) as `nextmsg` from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? and `msg_id` > ? $mid ";
		$result = $this->query($query,$bindvars,1,0);
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
		$query = "select max(`msg_id`) as `prevmsg` from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? and `msg_id` < ? $mid";
		$result = $this->query( $query, $bindvars, 1, 0 );
		$res = $result->fetchRow();

		if (!$res)
			return false;

		return $res['prevmsg'];
	}

	function get_message( $pUserId, $msg_id ) {
		$bindvars = array( $pUserId, (int)$msg_id );
		$query = "select * from `".BIT_DB_PREFIX."messu_messages` WHERE `to_user_id`=? and `msg_id`=?";
		$result = $this->query($query,$bindvars);
		$res = $result->fetchRow();
		$content = new LibertyContent();
		$res['parsed'] = $content->parseData( $res['body'], PLUGIN_GUID_TIKIWIKI );

		if (empty($res['subject']))
			$res['subject'] = tra('NONE');

		return $res;
	}

	/*shared*/
	function user_unread_messages( $pUserId ) {
		return $this->getOne( "select count( * ) from `".BIT_DB_PREFIX."messu_messages` where `to_user_id`=? and `is_read`=?",array( $pUserId,'n' ) );
	}
}

global $messulib;
$messulib = new Messu();

?>
