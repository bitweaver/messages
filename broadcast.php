<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/broadcast.php,v 1.5 2005/10/12 20:11:32 drewslater Exp $
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
$gBitSystem->verifyPermission( 'bit_p_broeadcast_messages' );

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
	$messulib->flag_message($user, $_REQUEST['msg_id'], 'is_replied', 'y');
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
		$messulib->post_system_message($_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['group']);
		$message = "Message successfully broadcast";
	}
	$gBitSmarty->assign('message', $message);
	$gBitSmarty->assign('errors', $errors);
}


if ($gBitUser->isAdmin()) {	
	$pListHash = array();
	$groups = $gBitUser->getAllGroups($pListHash);
} else {
	$gBitUser->loadGroups();
	$groups = &$gBitUser->mGroups; 
	
}

$gBitSmarty->assign('groups', $groups["data"]);

$section = 'user_messages';
$gBitSystem->display( 'bitpackage:messu/messu_broadcast.tpl');
?>
