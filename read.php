<?php
/**
* message package modules
*
* @author   
* @version  $Header$
* @package  messages
* @subpackage functions
*/

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

/**
 * required setup
 */
require_once( '../kernel/includes/setup_inc.php' );
require_once( MESSAGES_PKG_PATH.'Messages.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSystem->fatalError( tra( "You are not logged in" ) );
}

$gBitSystem->isPackageActive( 'messages' );
$gBitSystem->verifyPermission( 'p_messages_send' );

$messages = new Messages();

if( isset( $_REQUEST["msgdel"] ) ) {
	$messages->expungeMessage( $gBitUser->mUserId, $_REQUEST['msgdel'] );
}

if( !empty( $_REQUEST['act'] ) ) {
	$messages->flagMessage( $_REQUEST );
}

$gBitSmarty->assign( 'msg_id', $_REQUEST['msg_id'] );

// get prev / next messages
$listHash = $_REQUEST;
$listHash['neighbour'] = 'next';
$next = $messages->getNeighbourMessage( $listHash );
$listHash['neighbour'] = 'prev';
$prev = $messages->getNeighbourMessage( $listHash );
$gBitSmarty->assign('next', $next);
$gBitSmarty->assign('prev', $prev);

// Mark the message as read
$flagHash = array(
	'msg_id' => $_REQUEST['msg_id'],
	'act'    => 'is_read',
	'actval' => 'y',
);
$messages->flagMessage( $flagHash );

// Get the message and assign its data to template vars
$msg = $messages->getMessage( $gBitUser->mUserId, $_REQUEST['msg_id']);
$gBitSmarty->assign( 'msg', $msg );

$gBitSystem->display( 'bitpackage:messages/read.tpl', NULL, array( 'display_mode' => 'display' ));

?>
