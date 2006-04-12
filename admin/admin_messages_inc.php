<?php

// $Header: /cvsroot/bitweaver/_bit_messages/admin/admin_messages_inc.php,v 1.1 2006/04/12 06:38:35 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This doen't scale very well when you have 1000's of users
//$users_list = $gBitUser->get_users_names();
//$gBitSmarty->assign( 'users_list',$users_list );

$users_list = $gBitUser->get_users_names();
$gBitSmarty->assign( 'users_list', ( count( $users_list ) < 50 ) ? $users_list : NULL );

if( !empty( $_REQUEST['anonymous_settings'] ) ) {
	simple_set_toggle( "site_contact", MESSAGES_PKG_NAME );
	simple_set_value( "contact_user", MESSAGES_PKG_NAME );
}

$gBitSystem->setHelpInfo( 'Features', 'Settings', 'Help with the features settings' );
?>
