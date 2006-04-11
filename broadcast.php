<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/broadcast.php,v 1.10 2006/04/11 17:52:10 squareing Exp $
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

include_once( MESSAGES_PKG_PATH.'messages_lib.php' );

if (!$gBitUser->isRegistered()) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->isPackageActive( 'messages', TRUE );
$gBitSystem->verifyPermission( 'p_messages_broadcast' );

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



$gBitSmarty->assign('sent', 0);

if (isset($_REQUEST['reply']) || isset($_REQUEST['replyall'])) {
	$messageslib->flag_message($user, $_REQUEST['msg_id'], 'is_replied', 'y');
}


if (isset($_REQUEST['send'])) {
	$gBitSmarty->assign('sent', 1);
	$message = '';
	$errors = array();
	// Validation:
	// must have a subject or body non-empty (or both)
	if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
		$errors[] = tra("Subject or body must not be empty");
	}
	if (empty($_REQUEST['group'])) {
		$errors[] = tra("You must select a group to broadcast this message to");
	}

	if (!count($errors)) {
		$messageslib->post_system_message($_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['group']);
		$message = "Message successfully broadcast";
	}
	$gBitSmarty->assign('message', $message);
	$gBitSmarty->assign('errors', $errors);
}


if ($gBitUser->isAdmin()) {
	$pListHash = array('sort_mode' => 'group_id_asc');
	$groups = $gBitUser->getAllGroups($pListHash);
} else {
	$gBitUser->loadGroups();
	$groups = &$gBitUser->mGroups;
}

$gBitSmarty->assign('groups', $groups["data"]);

$gBitSystem->display( 'bitpackage:messages/messages_broadcast.tpl');
?>
