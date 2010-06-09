<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

$usersList = $gBitUser->getSelectionList();
$gBitSmarty->assign( 'usersList', ( count( $usersList ) < 50 ) ? $usersList : NULL );

if( !empty( $_REQUEST['anonymous_settings'] ) ) {
	simple_set_toggle( "messages_site_contact", MESSAGES_PKG_NAME );
	simple_set_value( "messages_contact_user", MESSAGES_PKG_NAME );
}

$gBitSystem->setHelpInfo( 'Features', 'Settings', 'Help with the features settings' );
?>
