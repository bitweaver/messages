<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'messages',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'messages' ) && $gBitUser->hasPermission( 'p_messages_send' ) ) {
	require_once( MESSAGES_PKG_PATH.'messages_lib.php' );
	$unreadMsgs = $messageslib->user_unread_messages( $gBitUser->mUserId );
	$gBitSmarty->assign_by_ref( 'unreadMsgs', $unreadMsgs );
}
?>
