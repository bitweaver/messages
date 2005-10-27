<?php

$tables = array(

'messu_messages' => "
	msg_id I4 AUTO PRIMARY,
	to_user_id I4 NOTNULL,
	from_user_id I4 NOTNULL,
	msg_to X,
	msg_cc X,
	msg_bcc X,
	subject C(255),
	body X,
	hash C(32),
	date I8,
	is_read C(1),
	is_replied C(1),
	is_flagged C(1),
	priority I4
"
//  CONSTRAINT	', CONSTRAINT tiki_messu_to_user_ref FOREIGN KEY (to_user_id) REFERENCES `".BIT_DB_PREFIX."users_users` (user_id)
//				 , CONSTRAINT tiki_messu_from_user_ref FOREIGN KEY (from_user_id) REFERENCES `".BIT_DB_PREFIX."users_users` (user_id)'

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( MESSU_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( MESSU_PKG_NAME, array(
	'description' => "An intra-site messaging system for users.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'beta',
	'dependencies' => '',
) );


// ### Default User Permissions
$gBitInstaller->registerUserPermissions( MESSU_PKG_NAME, array(
	array('bit_p_messages', 'Can use the messaging system', 'registered', 'messages'),
) );

// ### Indexes
$indices = array (
	'tiki_messu_to_user_id_idx' => array( 'table' => 'messu_messages', 'cols' => 'to_user_id', 'opts' => NULL ),
	'tiki_messu_from_user_id_idx' => array( 'table' => 'messu_messages', 'cols' => 'from_user_id', 'opts' => NULL )
);
// TODO - SPIDERR - following seems to cause time _decrease_ cause bigint on postgres. need more investigation
//	'tiki_blog_posts_created_idx' => array( 'table' => 'tiki_blog_posts', 'cols' => 'created', 'opts' => NULL ),
$gBitInstaller->registerSchemaIndexes( MESSU_PKG_NAME, $indices );

?>
