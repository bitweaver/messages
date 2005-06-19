{* $Header: /cvsroot/bitweaver/_bit_messages/modules/mod_unread_messages.tpl,v 1.1 2005/06/19 04:56:31 bitweaver Exp $ *}
{if $gBitUser->isRegistered() and $gBitSystem->isPackageActive( 'messu') and $gBitUser->hasPermission( 'bit_p_messages' )}
	{bitmodule title="$moduleTitle" name="messages_unread_messages"}
		<a href="{$gBitLoc.MESSU_PKG_URL}message_box.php">
			{tr}You have <strong>{$unreadMsgs} unread</strong> {if $unreadMsgs eq '1'}message{else}messages{/if}{/tr}
		</a>
	{/bitmodule}
{/if}
