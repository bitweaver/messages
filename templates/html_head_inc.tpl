{* not working currently if $unreadMsgs and $gBitUser->getPreference('messages_alert') == 'y' and !$smarty.cookies.messages_alert_sent}
	<script type="text/javascript">
		var redirect = confirm( '{tr}You have {$unreadMsgs} message(s) waiting for you in your personal message box. Click OK to view your messages.{/tr}' );
		if( redirect == true ) {ldelim}
			window.location="{$smarty.const.MESSAGES_PKG_URL}message_box.php";
		{rdelim}
		BitBase.setCookie( 'messages_alert_sent', true );
	</script>
{/if*}
