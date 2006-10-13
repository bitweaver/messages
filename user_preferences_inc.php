<?php
/**
* message package modules
*
* @author   
* @version  $Header: /cvsroot/bitweaver/_bit_messages/user_preferences_inc.php,v 1.2 2006/10/13 12:45:32 lsces Exp $
* @package  messages
* @subpackage functions
*/

$title = "User Messages";
if (isset($_REQUEST['messprefs'])) {
	$editUser->storePreference( 'messages_max_records', $_REQUEST['messages_max_records'], 'users' );
	$editUser->storePreference( 'messages_min_priority', $_REQUEST['messages_min_priority'], 'users' );
	$editUser->storePreference( 'messages_alert', !empty( $_REQUEST['messages_alert'] ) ? 'y' : 'n', 'users' );
	$editUser->storePreference( 'messages_allow_messages', !empty( $_REQUEST['messages_allow_messages'] ) ? 'y' : 'n', 'users' );
}
?>