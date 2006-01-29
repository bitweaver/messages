{if $unreadMsgs and $gBitUser->getPreference('message_alert')}
	<script type="text/javascript">
		var redirect = confirm( '{tr}You have {$unreadMsgs} message(s) waiting for you in your personal message box. Click OK to view your messages.{/tr}' );
		if( redirect == true && !getCookie( 'message_alert_sent' ) ) {ldelim}
			setCookie( 'message_alert_sent', true, 600 );
			window.location="{$smarty.const.MESSU_PKG_URL}message_box.php";
		{rdelim}
	</script>
{/if}
