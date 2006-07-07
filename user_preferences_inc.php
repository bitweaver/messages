<?php
$title = "User Messages";
if (isset($_REQUEST['messprefs'])) {
	$editUser->storePreference( 'messages_max_records', $_REQUEST['messages_max_records'], 'users' );
	$editUser->storePreference( 'messages_min_priority', $_REQUEST['messages_min_priority'], 'users' );
	$editUser->storePreference( 'messages_alert', !empty( $_REQUEST['messages_alert'] ) ? 'y' : 'n', 'users' );
	$editUser->storePreference( 'messages_allow_messages', !empty( $_REQUEST['messages_allow_messages'] ) ? 'y' : 'n', 'users' );
}
?>