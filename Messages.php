<?php
/**
* message package modules
*
* @author
* @version  $Revision$
* @package  messages
*/

/**
 * Messages base class
 *
 * @package  messages
 */
class Messages extends BitBase {

	/**
	 * postMessage 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function postMessage( $pParamHash ) {
		global $gBitSmarty, $gBitUser, $gBitSystem;

		if( $this->verifyMessage( $pParamHash )) {
			$this->mDb->associateInsert( BIT_DB_PREFIX."messages", $pParamHash['message_store'] );
			// we need to load the user this message is being sent to that we can check if the user should be notified by email
			$queryUser = new BitUser( $pParamHash['userInfo']['user_id'] );
			$queryUser->load();
			if( $queryUser->getPreference( 'messages_min_priority' ) && $queryUser->getPreference( 'messages_min_priority' ) <= $pParamHash['message_store']['priority'] ) {
				if( !empty( $pParamHash['userInfo']['email'] )) {
					$gBitSmarty->assign( 'msgHash', $pParamHash['message_store'] );
					$gBitSmarty->assign( 'from', stripslashes( $gBitUser->getDisplayName() ));

					@mail(
						$pParamHash['userInfo']['email'],
						tra( 'New message arrived from ' ).$gBitSystem->getConfig( 'kernel_server_name', $_SERVER["SERVER_NAME"] ),
						$gBitSmarty->fetch( 'bitpackage:messages/message_notification.tpl' ),
						"From: ".$gBitSystem->getConfig( 'site_sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n"
					);
				}
			}
		}

		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * verifyMessage 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function verifyMessage( &$pParamHash ) {
		global $gBitSystem, $gBitUser;

		if( !empty( $pParamHash['to_login'] ) ) {
			$pParamHash['userInfo'] = $userInfo = $gBitUser->getUserInfo( array( 'login' => $pParamHash['to_login'] ) );

			// if that didn't work, we'll see if we were passed a user_id
			if( empty( $userInfo ) && @BitBase::verifyId( $pParamHash['to_login'] ) ) {
				$userInfo = $gBitUser->getUserInfo( array( 'user_id' => $pParamHash['to_login'] ) );
			}
		} else {
			$this->mErrors['to_login'] = tra( 'No message recipient was specified' );
		}

		if( empty( $userInfo ) ) {
			$this->mErrors['compose'] = tra( 'Unknown user' ).": ".$pParamHash['to_login'];
		} elseif( empty( $this->mErrors ) && $gBitUser->getPreference( 'messages_allow_messages', 'y', $userInfo['user_id'] ) ) {
			// neither subject nor body may contain html - users can use tikiwiki syntax for styling
			if( !empty( $pParamHash['subject'] ) ) {
				$pParamHash['message_store']['subject'] = strip_tags( $pParamHash['subject'] );
			} else {
				$this->mErrors['subject'] = tra( "The message requires a subject" );
			}

			if( !empty( $pParamHash['body'] ) ) {
				$pParamHash['message_store']['body'] = strip_tags( $pParamHash['body'] );
			} else {
				$this->mErrors['body'] = tra( "The message requires a body" );
			}

			$pParamHash['message_store']['to_user_id']   = $userInfo['user_id'];
			$pParamHash['message_store']['from_user_id'] = $gBitUser->mUserId;
			$pParamHash['message_store']['msg_date']     = $gBitSystem->mServerTimestamp->getUTCTime();
			$pParamHash['message_store']['msg_to']       = !empty( $pParamHash['msg_to'] )     ? $pParamHash['msg_to']     : '';
			$pParamHash['message_store']['msg_cc']       = !empty( $pParamHash['msg_cc'] )     ? $pParamHash['msg_cc']     : NULL;
			$pParamHash['message_store']['msg_bcc']      = !empty( $pParamHash['msg_bcc'] )    ? $pParamHash['msg_bcc']    : NULL;
			$pParamHash['message_store']['is_read']      = !empty( $pParamHash['is_read'] )    ? $pParamHash['is_read']    : 'n';
			$pParamHash['message_store']['is_replied']   = !empty( $pParamHash['is_replied'] ) ? $pParamHash['is_replied'] : 'n';
			$pParamHash['message_store']['is_flagged']   = !empty( $pParamHash['is_flagged'] ) ? $pParamHash['is_flagged'] : 'n';
			$pParamHash['message_store']['priority']     = !empty( $pParamHash['priority'] )   ? $pParamHash['priority']   : '3';
			$pParamHash['message_store']['group_id']     = !empty( $pParamHash['group_id'] )   ? $pParamHash['group_id']   : NULL;

			if( empty( $this->mErrors ) ) {
				$pParamHash['message_store']['hash'] = md5( $pParamHash['subject'].$pParamHash['body'] );
				$query = "
					SELECT COUNT(*)
					FROM `".BIT_DB_PREFIX."messages`
					WHERE `to_user_id`=? AND `from_user_id`=? AND `hash`=?
				";
				$bindVars[] = $userInfo['user_id'];
				$bindVars[] = $gBitUser->mUserId;
				$bindVars[] = $pParamHash['message_store']['hash'];

				if( $this->mDb->getOne( $query, $bindVars ) ) {
					$this->mErrors['compose'] = $pParamHash['to_login'].' '.tra( 'has already received this message' );
				}
			}
		} else {
			$this->mErrors['allow_messages'] = tra( "This user doesn't want to recieve messages" );
		}

		return( count( $this->mErrors ) == 0 );
	}

	function getList( &$pListHash ) {
		global $gBitUser;

		// ====================== Private Messages ======================
		if( empty( $pListHash['sort_mode'] ) ) {
			$pListHash['sort_mode'] = 'msg_date_desc';
		}

		LibertyBase::prepGetList( $pListHash );

		$ret = $bindVars = array();
		$whereSql = '';
		$bindVars[] = $gBitUser->mUserId;

		if( !empty( $pListHash['priority'] ) ) {
			$whereSql .= " AND mm.`priority`=? ";
			$bindVars[] = $pListHash['priority'];
		}

		if( !empty( $pListHash['flag'] ) && !empty( $pListHash['flagval'] ) ) {
			$whereSql .= " AND mm.`{$pListHash['flag']}`=? ";
			$bindVars[] = $pListHash['flagval'];
		}

		if( !empty( $pListHash['find'] ) ) {
			$whereSql .= " AND( UPPER( mm.`subject` ) LIKE ? OR UPPER( mm.`body` ) LIKE ? ) ";
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
		}

		$query = "
			SELECT
				uu.`login`, uu.`real_name`, uu.`user_id`,
				mm.*
			FROM `".BIT_DB_PREFIX."messages` mm
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( mm.`from_user_id`=uu.`user_id` )
			WHERE mm.`to_user_id`=? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$normalMessages = $this->mDb->getAll( $query, $bindVars );

		// Get the total count of private messages
		$query = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."messages` mm WHERE mm.`to_user_id`=? $whereSql";
		$cant = $this->mDb->getOne( $query, $bindVars );




		// ====================== Broadcast Messages ======================
		//array_unshift( $bindVars, $gBitUser->mUserId, ROOT_USER_ID, $gBitUser->mUserId );
		$bindVars = array( $gBitUser->mUserId, ROOT_USER_ID, $gBitUser->mUserId );

		$whereSql = '';

		if( !empty( $pListHash['priority'] ) ) {
			$whereSql .= " AND mm.`priority`=? ";
			$bindVars[] = $pListHash['priority'];
		}

		if( !empty( $pListHash['flag'] ) && !empty( $pListHash['flagval'] ) ) {
			$whereSql .= " AND msm.`{$pListHash['flag']}`=? ";
			$bindVars[] = $pListHash['flagval'];
		}

		if( !empty( $pListHash['find'] ) ) {
			$whereSql .= " AND( UPPER( mm.`subject` ) LIKE ? OR UPPER( mm.`body` ) LIKE ? ) ";
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
		}

		$query = "
			SELECT
				uu.`login`, uu.`real_name`, uu.`user_id`, mm.`msg_id` as `msg_id_foo`,
				msm.*,
				mm.`msg_to`, mm.`msg_cc`, mm.`msg_bcc`, mm.`subject`, mm.`body`, mm.`hash`, mm.`msg_date`, mm.`priority`
			FROM `".BIT_DB_PREFIX."messages` mm
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (mm.`from_user_id` = uu.`user_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."messages_system_map` msm  ON (mm.`msg_id` = msm.`msg_id` AND msm.`to_user_id` = ?)
			WHERE mm.`to_user_id`=? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$result = $this->mDb->query( $query, $bindVars );

		$systemMessages = array();
		while( $aux = $result->fetchRow() ) {
			$aux['is_broadcast_message'] = TRUE;
			$aux['msg_id'] = $aux['msg_id_foo'];	// Due to the left outer join this madness is neccessary
			unset( $aux['msg_id_foo'] );
			if( $aux['is_hidden'] != 'y' ) {
				$systemMessages[] = $aux;
			}
		}

		$query = "
			SELECT COUNT(mm.`msg_id`)
			FROM `".BIT_DB_PREFIX."messages` mm
				LEFT OUTER JOIN `".BIT_DB_PREFIX."messages_system_map` msm ON (mm.`msg_id` = msm.`msg_id` AND msm.`to_user_id` = ?)
			WHERE mm.`to_user_id`=? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) $whereSql";
		$cant2 = $this->mDb->getOne($query, $bindVars);




		// ====================== insane message mergin and sorting ======================
		$sort_mode = $pListHash['sort_mode'];
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

		// set some default values
		foreach( $ret as $key => $msg ) {
			$msg['len'] = strlen( $msg['body'] );
			if( empty( $msg['is_read'] ) ) {
				$msg['is_read'] = 'n';
			}
			if( empty( $msg['subject'] ) ) {
				$msg['subject'] = tra( 'none' );
			}
			$ret[$key] = $msg;
		}


		$pListHash["cant"] = $cant + $cant2;
		LibertyBase::postGetList( $pListHash );
		return $ret;
	}

	//function flagMessage( $pUserId, $msg_id, $flag, $val ) {
	function flagMessage( $pFlagHash ) {
		global $gBitUser;
		if( !@BitBase::verifyId( $pFlagHash['msg_id'] ) ) {
			return FALSE;
		}

		if( $this->isSystemMessage($pFlagHash['msg_id'] )) {
			$query = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."messages_system_map` WHERE `to_user_id` = ? AND `msg_id` = ?";
			$rowExists = $this->mDb->getOne( $query, array( $gBitUser->mUserId, $pFlagHash['msg_id'] ) );
			if( $rowExists ) {
				$query = "UPDATE `".BIT_DB_PREFIX."messages_system_map` SET `{$pFlagHash['act']}`=? WHERE `to_user_id` = ? AND `msg_id` = ?";
				$this->mDb->query( $query, array( $pFlagHash['actval'], $gBitUser->mUserId, (int)$pFlagHash['msg_id'] ) );
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."messages_system_map` (`msg_id`, `to_user_id`, `{$pFlagHash['act']}`) VALUES (?,?,?)";
				$this->mDb->query( $query, array( (int)$pFlagHash['msg_id'], $gBitUser->mUserId, $pFlagHash['actval'] ) );
			}

		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."messages` SET `{$pFlagHash['act']}`=? where `to_user_id`=? and `msg_id`=?";
			$this->mDb->query( $query, array( $pFlagHash['actval'], $gBitUser->mUserId, (int)$pFlagHash['msg_id'] ) );
		}
	}

	function expungeMessage($pUserId, $msg_id) {
		if (!$msg_id)
			return false;
		if ($this->isSystemMessage($msg_id)) {
			// We just mark this user's messages_system_map row is_hidden = 'y'
			$query = "UPDATE `".BIT_DB_PREFIX."messages_system_map` SET `is_hidden` = 'y' WHERE `to_user_id` = ? AND `msg_id` = ?";
			$this->mDb->query($query, array($pUserId, $msg_id));
		} else {
			$query = "delete from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `msg_id`=?";
			$this->mDb->query($query,array($pUserId,(int)$msg_id));
		}
	}

	function getNeighbourMessage( &$pListHash ) {
		global $gBitUser;

		if( !@BitBase::verifyId( $pListHash['msg_id'] ) ) {
			return FALSE;
		}

		$ret = $bindVars = array();
		$whereSql = '';
		$bindVars[] = $gBitUser->mUserId;
		$bindVars[] = $pListHash['msg_id'];

		if( !empty( $pListHash['priority'] ) ) {
			$whereSql .= " AND mm.`priority`=? ";
			$bindVars[] = $pListHash['priority'];
		}

		if( !empty( $pListHash['flag'] ) && !empty( $pListHash['flagval'] ) ) {
			$whereSql .= " AND mm.`{$pListHash['flag']}`=? ";
			$bindVars[] = $pListHash['flagval'];
		}

		if( !empty( $pListHash['find'] ) ) {
			$whereSql .= " AND( UPPER( mm.`subject` ) LIKE ? OR UPPER( mm.`body` ) LIKE ? ) ";
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
			$bindVars[] = '%'.strtoupper( $pListHash['find'] ).'%';
		}

		if( !empty( $pListHash['neighbour'] ) && $pListHash['neighbour'] == 'prev' ) {
			$query = "SELECT MAX(`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm WHERE `to_user_id`=? AND `msg_id` < ? $whereSql";
		} else {
			$query = "SELECT MIN(`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm WHERE `to_user_id`=? AND `msg_id` > ? $whereSql";
		}

		$msg_id = $this->mDb->getOne( $query, $bindVars );
		return( !empty( $msg_id ) ? $msg_id : FALSE );
	}

	function getMessage( $pUserId, $msg_id ) {
		if (!$this->isSystemMessage($msg_id)) {
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

		global $gBitSystem;
		$res['parsed'] = LibertyContent::parseDataHash( $res['body'], $gBitSystem->getConfig( 'default_format') );

		if (empty($res['subject']))
			$res['subject'] = tra('NONE');

		return $res;
	}

	/*shared*/
	function unreadMessages( $pUserId ) {
		// Standard user to user messages
		$normalCount =  $this->mDb->getOne( "select count( * ) from `".BIT_DB_PREFIX."messages` where `to_user_id`=? and `is_read`=?",array( $pUserId,'n' ) );
		// Broadcast messages where they have a messages_system_map row but is_read is not yet set
		$broadcastCount = $this->mDb->getOne("SELECT COUNT(mm.`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm INNER JOIN `".BIT_DB_PREFIX."messages_system_map` msm ON (mm.`msg_id` = msm.`msg_id` AND msm.`is_read` <> 'y' AND `is_hidden` <> 'y' AND msm.`to_user_id`= ?) WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id`= ?) ", array($pUserId, ROOT_USER_ID, $pUserId));
		// Broadcast messages where they do not yet have a messages_system_map row
		$broadcastCount2 = $this->mDb->getOne("SELECT COUNT(mm.`msg_id`) FROM `".BIT_DB_PREFIX."messages` mm WHERE mm.`to_user_id` = ? AND mm.`group_id` IN (SELECT `group_id` FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `user_id` = ?) AND NOT EXISTS ( SELECT msm.`msg_id` FROM `".BIT_DB_PREFIX."messages_system_map` msm WHERE msm.`msg_id` = mm.`msg_id` AND msm.`to_user_id` = ?)", array(ROOT_USER_ID, $pUserId, $pUserId));
		return $normalCount + $broadcastCount + $broadcastCount2;
	}



	// ==================== system messages ====================
	function postSystemMessage( $pParamHash ) {
		$pParamHash['to_login'] = ROOT_USER_ID;
		$pParamHash['to']       = ROOT_USER_ID;

		if( @BitBase::verifyId( $pParamHash['group_id'] ) ) {
			return $this->postMessage( $pParamHash );
		} else {
			$this->mErrors['group_id'] = tra( "You need to specify a group id to broadcast the message to." );
			return FALSE;
		}
	}

	function getSystemMessageList() {
		$sql = "
			SELECT *
			FROM `".BIT_DB_PREFIX."messages`
			WHERE `from_user_id` = ? AND `group_id` IS NOT NULL
		";
		$res = $this->mDb->query( $sql, array( ROOT_USER_ID ) );
		return $res->getRows();
	}

	function expungeSystemMessage( $pMessageId = NULL ) {
		if( @BitBase::verifyId( $pMessageId ) ) {
			$tables = array( "messages_system_map", "messages" );
			foreach( $tables as $table ) {
				$sql = "DELETE FROM `".BIT_DB_PREFIX.$table."` WHERE msg_id = ?";
				$rs = $this->mDb->query( $sql, array( $pMessageId ) );
			}
		}
	}

	function isSystemMessage( $pMessageId = NULL ) {
		$ret = FALSE;
		if( @BitBase::verifyId( $pMessageId ) ) {
			$query = "SELECT COUNT(msg_id) FROM `".BIT_DB_PREFIX."messages` WHERE `to_user_id` = ? AND `msg_id` = ?";
			$ret = $this->mDb->getOne( $query, array( ROOT_USER_ID, $pMessageId ) );
		}
		return $ret;
	}

}
