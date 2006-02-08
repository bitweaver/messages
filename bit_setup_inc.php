<?php
global $gBitSystem;
$gBitSystem->registerPackage( 'messages', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'messages' ) && $gBitUser->hasPermission( 'bit_p_messages' ) ) {
	require_once( MESSAGES_PKG_PATH.'messages_lib.php' );
	$unreadMsgs = $messageslib->user_unread_messages( $gBitUser->mUserId );
	$gBitSmarty->assign_by_ref( 'unreadMsgs', $unreadMsgs );
}
?>
