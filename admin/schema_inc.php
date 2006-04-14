<?php

$tables = array(

'messages' => "
	msg_id I4 AUTO PRIMARY,
	to_user_id I4 NOTNULL,
	from_user_id I4 NOTNULL,
	msg_to X,
	msg_cc X,
	msg_bcc X,
	subject C(255),
	body X,
	hash C(32),
	msg_date I8,
	is_read C(1),
	is_replied C(1),
	is_flagged C(1),
	group_id I4,
	priority I4
",

'messages_system_map' => "
	msg_id I4,
	to_user_id I4 NOTNULL,
	is_read C(1),
	is_flagged C(1),
	is_replied C(1),
	priority I4,
	is_hidden C(1)
	CONSTRAINT	', CONSTRAINT `messages_system_message_ref` FOREIGN KEY (`msg_id`) REFERENCES `".BIT_DB_PREFIX."messages` (`msg_id`)'
"

//  CONSTRAINT	', CONSTRAINT messages_to_user_ref FOREIGN KEY (to_user_id) REFERENCES `".BIT_DB_PREFIX."users_users` (user_id)
//				 , CONSTRAINT messages_from_user_ref FOREIGN KEY (from_user_id) REFERENCES `".BIT_DB_PREFIX."users_users` (user_id)'

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( MESSAGES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( MESSAGES_PKG_NAME, array(
	'description' => "An intra-site messaging system for users.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'beta',
	'dependencies' => '',
) );


// ### Default User Permissions
$gBitInstaller->registerUserPermissions( MESSAGES_PKG_NAME, array(
	array('p_messages_send', 'Can use the messaging system', 'registered', MESSAGES_PKG_NAME),
	array('p_messages_broadcast', 'Can send internal messages to all users', 'editors', MESSAGES_PKG_NAME),
) );

// ### Indexes
$indices = array (
	'messages_to_user_id_idx' => array( 'table' => 'messages', 'cols' => 'to_user_id', 'opts' => NULL ),
	'messages_from_user_id_idx' => array( 'table' => 'messages', 'cols' => 'from_user_id', 'opts' => NULL )
);
// TODO - SPIDERR - following seems to cause time _decrease_ cause bigint on postgres. need more investigation
//	'blog_posts_created_idx' => array( 'table' => 'blog_posts', 'cols' => 'created', 'opts' => NULL ),
$gBitInstaller->registerSchemaIndexes( MESSAGES_PKG_NAME, $indices );

// ### Default Preferences
$gBitInstaller->registerPreferences( MESSAGES_PKG_NAME, array(
	//array(MESSAGES_PKG_NAME,'messages_site_contact','n'),
	array(MESSAGES_PKG_NAME,'messages_contact_user','admin'),
) );
?>
