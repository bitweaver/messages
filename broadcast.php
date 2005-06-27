<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/broadcast.php,v 1.1.1.1.2.1 2005/06/27 14:42:29 lsces Exp $
* @package  messages
* @subpackage functions
*/

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

include_once( MESSU_PKG_PATH.'messu_lib.php' );

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if ($feature_messages != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_messages");

	$gBitSystem->display( 'error.tpl' );
	die;
}

if ($bit_p_broadcast != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST['to']))
	$_REQUEST['to'] = '';

if (!isset($_REQUEST['cc']))
	$_REQUEST['cc'] = '';

if (!isset($_REQUEST['bcc']))
	$_REQUEST['bcc'] = '';

if (!isset($_REQUEST['subject']))
	$_REQUEST['subject'] = '';

if (!isset($_REQUEST['body']))
	$_REQUEST['body'] = '';

if (!isset($_REQUEST['priority']))
	$_REQUEST['priority'] = 3;

$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);

$gBitSystem->display( 'bitpackage:messu/messu_broadcast.tpl');

$smarty->assign('sent', 0);

if (isset($_REQUEST['reply']) || isset($_REQUEST['replyall'])) {
	$messulib->flag_message($user, $_REQUEST['msg_id'], 'is_replied', 'y');
}

if (isset($_REQUEST['group'])) {
	if ($_REQUEST['group'] == 'all') {
		$a_all_users = $userlib->get_users(0, -1, 'login_desc', '');

		$all_users = array();

		foreach ($a_all_users['data'] as $a_user) {
			$all_users[] = $a_user['user'];
		}
	} else {
		$all_users = $userlib->get_group_users($_REQUEST['group']);
	}
}

if (isset($_REQUEST['send'])) {
	
	$smarty->assign('sent', 1);

	$message = '';

	// Validation:
	// must have a subject or body non-empty (or both)
	if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
		$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

				die;
	}

	// Remove invalid users from the to, cc and bcc fields
	$users = array();

	foreach ($all_users as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->get_user_preference($a_user, 'allowMsgs', 'y')) {
					$users[] = $a_user;
				} else {
					// TODO: needs translation as soon as there is a solution for strings with embedded variables
					$message .= "User $a_user can not receive messages<br/>";
				}
			} else {
				$message .= tra("Invalid user"). "$a_user<br/>";
			}
		}
	}

	$users = array_unique($users);

	// Validation: either to, cc or bcc must have a valid user
	if (count($users) > 0) {
		$message .= tra("Message will be sent to: <ul><li>"). implode('<li> ', $users). "</ul><br/>";
	} else {
		$message = tra('ERROR: No valid users to send the message');

		$smarty->assign('message', $message);
				die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message($a_user, $user, $a_user, '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
	}

	$smarty->assign('message', $message);
}


$groups = $userlib->get_groups(0, -1, 'group_name_asc', '');
$smarty->assign_by_ref('groups', $groups["data"]);

$section = 'user_messages';
?>
