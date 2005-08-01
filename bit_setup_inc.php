<?php
global $gBitSystem;
$gBitSystem->registerPackage( 'messu', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'messu' ) && $gBitUser->hasPermission( 'bit_p_messages' ) ) {
	require_once( MESSU_PKG_PATH.'messu_lib.php' );
	$unreadMsgs = $messulib->user_unread_messages( $gBitUser->mUserId );
	$gBitSmarty->assign_by_ref( 'unreadMsgs', $unreadMsgs );
}
?>
