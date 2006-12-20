<?php
/**
* message package modules
*
* @author
* @version  $Header: /cvsroot/bitweaver/_bit_messages/message_box.php,v 1.14 2006/12/20 20:50:17 squareing Exp $
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
require_once( MESSAGES_PKG_PATH.'Messages.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->isPackageActive( 'messages', TRUE );
$gBitSystem->verifyPermission( 'p_messages_send' );

$messages = new Messages();

$max_records = $gBitSystem->getConfig( 'max_records', 20 );

// Mark messages if the mark button was pressed
if (isset($_REQUEST["mark"]) && isset($_REQUEST["msg"])) {
	foreach (array_keys($_REQUEST["msg"])as $msg) {
		$parts = explode('_', $_REQUEST['action']);
		$messages->flagMessage($gBitUser->mUserId, $msg, $parts[0].'_'.$parts[1], $parts[2]);
	}
}

// Delete messages if the delete button was pressed
if( !empty( $_REQUEST["delete"] ) && !empty( $_REQUEST["msg"] ) ) {
	foreach( array_keys( $_REQUEST["msg"] ) as $msg ) {
		$messages->expunge( $gBitUser->mUserId, $msg );
	}
}

if( !empty( $_REQUEST['filter'] ) ) {
	if( $_REQUEST['flags'] != '' ) {
		$parts = explode('_', $_REQUEST['flags']);

		$_REQUEST['flag'] = substr( $_REQUEST['flags'], 0, strrpos( $_REQUEST['flags'], '_' ) );
		$_REQUEST['flagval'] = substr( $_REQUEST['flags'], strrpos( $_REQUEST['flags'], '_' ) + 1 );
	}
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'msg_date_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign_by_ref('flagval', $_REQUEST['flagval']);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$gBitSmarty->assign('find', $find);
// What are we paginating: items
//$items = $messages->list_messages( $gBitUser->mUserId, $offset, $max_records, $sort_mode,
//	$find, $_REQUEST["flag"], $_REQUEST["flagval"], $_REQUEST['priority']);

$listHash = $_REQUEST;
$items = $messages->getList( $listHash );
$gBitSmarty->assign( 'items', $items );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );

$gBitSystem->display( 'bitpackage:messages/mailbox.tpl', 'Message box' );
?>
