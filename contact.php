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
require_once( MESSAGES_PKG_CLASS_PATH.'Messages.php' );

$gBitSystem->verifyFeature( 'messages_site_contact' );
$messages = new Messages();

if( !empty( $_REQUEST['send'] )) {
	if( empty( $_REQUEST['subject'] ) && empty( $_REQUEST['body'] ) ) {
		$gBitSystem->fatalError( tra( "Either a subject or a message body is required." ));
	}

	$postHash = $_REQUEST;
	$postHash['to_login'] = $postHash['msg_to'] = $gBitSystem->getConfig( 'messages_contact_user' );
	$messages->postMessage( $postHash );

	$feedback['success'] = tra( 'Your message was sent to' ).': '.$gBitSystem->getConfig( 'messages_contact_user' );
	$gBitSmarty->assign( 'feedback', $feedback );
}

$gBitSystem->display( 'bitpackage:messages/contact.tpl', NULL, array( 'display_mode' => 'display' ));
?>
