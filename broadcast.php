<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/broadcast.php,v 1.1.1.1.2.7 2005/10/29 18:06:26 squareing Exp $
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

if (!$gBitUser->isRegistered()) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->isPackageActive( 'messu', TRUE );
$gBitSystem->verifyPermission( 'bit_p_broeadcast' );

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

$gBitSmarty->assign('to', $_REQUEST['to']);
$gBitSmarty->assign('cc', $_REQUEST['cc']);
$gBitSmarty->assign('bcc', $_REQUEST['bcc']);
$gBitSmarty->assign('subject', $_REQUEST['subject']);
$gBitSmarty->assign('body', $_REQUEST['body']);
$gBitSmarty->assign('priority', $_REQUEST['priority']);

$gBitSystem->display( 'bitpackage:messu/messu_broadcast.tpl');

$gBitSmarty->assign('sent', 0);

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
	
	$gBitSmarty->assign('sent', 1);

	$message = '';

	// Validation:
	// must have a subject or body non-empty (or both)
	if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
		$gBitSmarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

				die;
	}

	// Remove invalid users from the to, cc and bcc fields
	$users = array();

	foreach ($all_users as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->getPreference('allowMsgs', 'y',$a_user )) {
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

		$gBitSmarty->assign('message', $message);
				die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message($a_user, $user, $a_user, '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
	}

	$gBitSmarty->assign('message', $message);
}


$groups = $userlib->get_groups(0, -1, 'group_name_asc', '');
$gBitSmarty->assign_by_ref('groups', $groups["data"]);

$section = 'user_messages';
?>
