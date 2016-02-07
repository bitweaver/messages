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
require_once( '../kernel/setup_inc.php' );
require_once( MESSAGES_PKG_PATH.'Messages.php' );

if( !$gBitUser->isRegistered() ) {
	$gBitSmarty->assign('msg', tra("You are not logged in"));
	$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'display' ));
	die;
}

$gBitSystem->isPackageActive( 'messages', TRUE );
$gBitSystem->verifyPermission( 'p_messages_send' );

$messages = new Messages();

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

if( !empty( $_REQUEST['action']['reply'] ) || !empty( $_REQUEST['action']['replyall'] ) ) {
	$replyToUser = $gBitUser->getUserInfo( array( 'user_id' => $_REQUEST['replyto'] ) );
	$_REQUEST['to'] = $replyToUser['login'];
	if( !empty( $_REQUEST['action']['replyall'] ) ) {
		$_REQUEST['cc'] = preg_replace( "/".$replyToUser['login'].",/", "", $_REQUEST['replyallto'] );
		$_REQUEST['cc'] = preg_replace( "/".$gBitUser->mUsername.",/", "", $_REQUEST['cc'] );
	}
}

// Strip Re:Re:Re: from subject
if(isset($_REQUEST['action']['reply']) || isset($_REQUEST['action']['replyall'])) {
	$_REQUEST['subject'] = tra("Re: ").preg_replace("/^(".tra("Re: ").")+/i", "", $_REQUEST['subject']);
}

$gBitSmarty->assign('to', $_REQUEST['to']);
$gBitSmarty->assign('cc', $_REQUEST['cc']);
$gBitSmarty->assign('bcc', $_REQUEST['bcc']);
$gBitSmarty->assign('subject', $_REQUEST['subject']);
$gBitSmarty->assign('body', $_REQUEST['body']);
$gBitSmarty->assign('priority', $_REQUEST['priority']);

$gBitSmarty->assign('sent', 0);
$feedback = array();
$gBitSmarty->assignByRef( 'feedback', $feedback );

if (isset($_REQUEST['replyto']) || isset($_REQUEST['replyallto'])) {
	$flagHash = array(
		'msg_id' => $_REQUEST['msg_id'],
		'act'    => 'is_replied',
		'actval' => 'y',
	);
	$messages->flagMessage( $flagHash );
}

if (isset($_REQUEST['send'])) {
	$message = '';
	// Validation:
	// must have a subject or body non-empty (or both)
	if ( !empty($_REQUEST['subject']) && !empty($_REQUEST['body'])) {
		// Parse the to, cc and bcc fields into an array
		$arrTo = explode( ',', preg_replace( '/ /', '', $_REQUEST['to'] ) );
		$arrCc = explode( ',', preg_replace( '/ /', '', $_REQUEST['cc'] ) );
		$arrBcc = explode( ',', preg_replace( '/ /', '', $_REQUEST['bcc'] ) );

		$toUsers = array_unique( array_merge( $arrTo, $arrCc, $arrBcc ) );
		// Validation: either to, cc or bcc must have a valid user
		if( count($toUsers) ) {
			// Insert the message in the inboxes of each user
			foreach ($toUsers as $toUser) {
				if( !empty( $toUser ) ) {
					$_REQUEST['to_login'] = $toUser;
					if( $messages->postMessage( $_REQUEST ) ) {
						$feedback['success'][] =  tra( "Message will be sent to: " ).' '.$toUser;
					} else {
						$feedback['error'][] = $messages->mErrors['compose'];
					}
				}
			}
			$gBitSmarty->assign('sent', 1);
		} else {
			$feedback['error'][] = tra('ERROR: No valid users to send the message.');
		}
	} else {
		$feedback['error'][] = tra( 'ERROR: Either the subject or body must contain text.' );
	}
}

$gBitSystem->display( 'bitpackage:messages/compose.tpl', 'Compose Message' , array( 'display_mode' => 'display' ));
?>
