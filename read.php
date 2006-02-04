<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/read.php,v 1.5 2006/02/04 19:04:34 squareing Exp $
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
require_once( MESSU_PKG_PATH.'messages_lib.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->isPackageActive( 'messu', TRUE );
$gBitSystem->verifyPermission( 'bit_p_messages' );

if (isset($_REQUEST["msgdel"])) {
	
	$messulib->delete_message($gBitUser->mUserId, $_REQUEST['msgdel']);
}

$sort_mode = !empty( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : '';
$find = !empty( $_REQUEST['find'] ) ? $_REQUEST['find'] : '';
$flag = !empty( $_REQUEST['flag'] ) ? $_REQUEST['flag'] : '';
$offset = !empty( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : '';
$flagval = !empty( $_REQUEST['flagval'] ) ? $_REQUEST['flagval'] : '';
$priority = !empty( $_REQUEST['priority'] ) ? $_REQUEST['priority'] : '';

$gBitSmarty->assign('sort_mode', $sort_mode );
$gBitSmarty->assign('find', $find );
$gBitSmarty->assign('flag', $flag );
$gBitSmarty->assign('offset', $offset );
$gBitSmarty->assign('flagval', $flagval );
$gBitSmarty->assign('priority', $priority );
$gBitSmarty->assign('legend', '');

if (!isset($_REQUEST['msg_id']) || $_REQUEST['msg_id'] == 0) {
	$gBitSmarty->assign('legend', tra("No more messages"));
	$gBitSystem->display( 'messu-read.tpl');
	die;
}

if (isset($_REQUEST['act'])) {
	$messulib->flag_message( $gBitUser->mUserId, $_REQUEST['msg_id'], $_REQUEST['act'], $_REQUEST['actval'] );
}

// Using the sort_mode, flag, flagval and find get the next and prev messages
$gBitSmarty->assign('msg_id', $_REQUEST['msg_id']);
$next = $messulib->get_next_message( $gBitUser->mUserId, $_REQUEST['msg_id'], $sort_mode, $find, $flag, $flagval, $priority );
$prev = $messulib->get_prev_message( $gBitUser->mUserId, $_REQUEST['msg_id'], $sort_mode, $find, $flag, $flagval, $priority );
$gBitSmarty->assign('next', $next);
$gBitSmarty->assign('prev', $prev);

// Mark the message as read
$messulib->flag_message( $gBitUser->mUserId, $_REQUEST['msg_id'], 'is_read', 'y');

// Get the message and assign its data to template vars
$msg = $messulib->get_message( $gBitUser->mUserId, $_REQUEST['msg_id']);
$gBitSmarty->assign('msg', $msg);

$gBitSystem->display( 'bitpackage:messu/messages_read.tpl');

?>
