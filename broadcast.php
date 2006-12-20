<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/broadcast.php,v 1.11 2006/12/20 20:50:17 squareing Exp $
* @package  messages
* @subpackage functions
*/

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

include_once( MESSAGES_PKG_PATH.'Messages.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSmarty->fatalError( tra( "You are not logged in" ) );
}

$gBitSystem->isPackageActive( 'messages', TRUE );
$gBitSystem->verifyPermission( 'p_messages_broadcast' );

$messages = new Messages();

// Configure quicktags list
if( $gBitSystem->getConfig( 'package_quicktags','n' ) == 'y' ) {
	include_once( QUICKTAGS_PKG_PATH.'quicktags_inc.php' );
}

$feedback = array();

if( isset( $_REQUEST['send'] ) ) {
	if( $messages->postSystemMessage( $_REQUEST ) ) {
		$feedback['success'] = "Message successfully broadcast";
	} else {
		$feedback['error'] = $messages->mErrors;
	}
}

if( $gBitUser->isAdmin() ) {
	$pListHash = array('sort_mode' => 'group_id_asc');
	$groups = $gBitUser->getAllGroups( $pListHash );
} else {
	$gBitUser->loadGroups();
	$groups = &$gBitUser->mGroups;
}
$gBitSmarty->assign( 'groups', $groups["data"] );
$gBitSmarty->assign( 'feedback', $feedback );

$gBitSystem->display( 'bitpackage:messages/broadcast.tpl');
?>
