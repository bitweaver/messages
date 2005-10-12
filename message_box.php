<?php
/**
* message package modules
*
* @author
* @version  $Header: /cvsroot/bitweaver/_bit_messages/message_box.php,v 1.1.1.1.2.3 2005/10/12 14:14:35 spiderr Exp $
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
require_once( MESSU_PKG_PATH.'messu_lib.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->isPackageActive( 'messu', TRUE );
$gBitSystem->verifyPermission( 'bit_p_messages' );

$maxRecords = $gBitSystem->getPreference( 'maxRecords', 20 );

// Mark messages if the mark button was pressed
if (isset($_REQUEST["mark"]) && isset($_REQUEST["msg"])) {
	foreach (array_keys($_REQUEST["msg"])as $msg) {
		$parts = explode('_', $_REQUEST['action']);
		$messulib->flag_message($gBitUser->mUserId, $msg, $parts[0].'_'.$parts[1], $parts[2]);
	}
}

// Delete messages if the delete button was pressed
if (isset($_REQUEST["delete"]) && isset($_REQUEST["msg"])) {

	foreach (array_keys($_REQUEST["msg"])as $msg) {
		$messulib->delete_message( $gBitUser->mUserId, $msg );
	}
}

if (isset($_REQUEST['filter'])) {
	if ($_REQUEST['flags'] != '') {
		$parts = explode('_', $_REQUEST['flags']);

		$_REQUEST['flag'] = substr( $_REQUEST['flags'], 0, strrpos( $_REQUEST['flags'], '_' ) );
		$_REQUEST['flagval'] = substr( $_REQUEST['flags'], strrpos( $_REQUEST['flags'], '_' ) + 1 );
	}
}

if (!isset($_REQUEST["priority"]))
	$_REQUEST["priority"] = '';

if (!isset($_REQUEST["flag"]))
	$_REQUEST["flag"] = '';

if (!isset($_REQUEST["flagval"]))
	$_REQUEST["flagval"] = '';

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'date_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign_by_ref('flag', $_REQUEST['flag']);
$gBitSmarty->assign_by_ref('priority', $_REQUEST['priority']);
$gBitSmarty->assign_by_ref('flagval', $_REQUEST['flagval']);
$gBitSmarty->assign_by_ref('offset', $offset);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$gBitSmarty->assign('find', $find);
// What are we paginating: items
$items = $messulib->list_messages( $gBitUser->mUserId, $offset, $maxRecords, $sort_mode,
	$find, $_REQUEST["flag"], $_REQUEST["flagval"], $_REQUEST['priority']);

$cant_pages = ceil($items["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('items', $items["data"]);

$section = 'user_messages';



$gBitSystem->display( 'bitpackage:messu/messu_mailbox.tpl', 'Message box' );
?>
