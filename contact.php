<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/contact.php,v 1.12 2007/04/23 09:36:31 squareing Exp $
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

if( !$gBitSystem->isFeatureActive( 'messages_site_contact' ) ) {
	$gBitSystem->fatalError( tra( "The Contact feature is disabled." ));
}

include_once( MESSAGES_PKG_PATH.'Messages.php' );
$messages = new Messages();

$userInfo = $gBitUser->getUserInfo( array( 'login' => $gBitSystem->getConfig( 'messages_contact_user' ) ) );
$email = $userInfo['email'];
if( empty( $email ) ) {
	$gBitSystem->fatalError( tra( "This feature is not correctly set up. The email address is missing." ));
} else {
	$gBitSmarty->assign( 'email', $email );
}

if (!empty($_REQUEST['send'])) {
	if( empty( $_REQUEST['subject'] ) && empty( $_REQUEST['body'] ) ) {
		$gBitSystem->fatalError( tra( "Either a subject or a message body is required." ));
	}
	$messages->postMessage( $userInfo['login'], $gBitUser->mUsername, $_REQUEST['to'], '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
	$feedback['success'] = tra( 'Your message was sent to' ).': '.( !empty( $userInfo['real_name'] ) ? $userInfo['real_name'] : $userInfo['login'] );
	$gBitSmarty->assign( 'feedback', $feedback );
}

$gBitSystem->display( 'bitpackage:messages/contact.tpl');
?>
