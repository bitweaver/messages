<?php

// $Header: /cvsroot/bitweaver/_bit_messages/admin/admin_messages_inc.php,v 1.4 2009/10/01 13:45:44 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

$usersList = $gBitUser->getSelectionList();
$gBitSmarty->assign( 'usersList', ( count( $usersList ) < 50 ) ? $usersList : NULL );

if( !empty( $_REQUEST['anonymous_settings'] ) ) {
	simple_set_toggle( "messages_site_contact", MESSAGES_PKG_NAME );
	simple_set_value( "messages_contact_user", MESSAGES_PKG_NAME );
}

$gBitSystem->setHelpInfo( 'Features', 'Settings', 'Help with the features settings' );
?>
