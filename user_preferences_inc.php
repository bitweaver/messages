<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/user_preferences_inc.php,v 1.4 2007/03/01 08:26:16 squareing Exp $
* @package  messages
* @subpackage functions
*/

$title = "User Messages";
if( isset( $_REQUEST['messprefs'] ) ) {
	$editUser->storePreference( 'messages_alert', !empty( $_REQUEST['messages_alert'] ) ? 'y' : 'n', USERS_PKG_NAME );
	$editUser->storePreference( 'messages_max_records', $_REQUEST['messages_max_records'], USERS_PKG_NAME );
	$editUser->storePreference( 'messages_min_priority', $_REQUEST['messages_min_priority'], USERS_PKG_NAME );
	$editUser->storePreference( 'messages_allow_messages', !empty( $_REQUEST['messages_allow_messages'] ) ? 'y' : 'n', USERS_PKG_NAME );
}
?>
