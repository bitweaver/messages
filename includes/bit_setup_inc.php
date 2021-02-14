<?php
global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'messages',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'messages' ) && $gBitUser->hasPermission( 'p_messages_send' ) ) {
	require_once( MESSAGES_PKG_CLASS_PATH.'Messages.php' );
	$messages = new Messages();
	$unreadMsgs = $messages->unreadMessages( $gBitUser->mUserId );
	$gBitSmarty->assignByRef( 'unreadMsgs', $unreadMsgs );
}
?>
