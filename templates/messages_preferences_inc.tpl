{strip}
{form legend="User Messages"}
	<div class="row">
		{formlabel label="Personal Messages" for=""}
		{forminput}
			<a href="{$smarty.const.MESSU_PKG_URL}message_box.php">
				{tr}You have <strong>{$unreadMsgs|default:no} unread</strong> {if $unreadMsgs eq '1'}message{else}messages{/if}{/tr}
			</a>
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Messages per page" for="mess_max_records"}
		{forminput}
			<select name="mess_max_records" id="mess_max_records">
				<option value="2"  {if $userPrefs.mess_max_records eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
				<option value="5"  {if $userPrefs.mess_max_records eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
				<option value="10" {if $userPrefs.mess_max_records eq 10}selected="selected"{/if}>{tr}10{/tr}</option>
				<option value="20" {if $userPrefs.mess_max_records eq 20}selected="selected"{/if}>{tr}20{/tr}</option>
				<option value="30" {if $userPrefs.mess_max_records eq 30}selected="selected"{/if}>{tr}30{/tr}</option>
				<option value="40" {if $userPrefs.mess_max_records eq 40}selected="selected"{/if}>{tr}40{/tr}</option>
				<option value="50" {if $userPrefs.mess_max_records eq 50}selected="selected"{/if}>{tr}50{/tr}</option>
			</select>
			{formhelp note=""}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Allow messages from other users" for="allowMsgs"}
		{forminput}
			<input type="checkbox" name="allowMsgs" id="allowMsgs" {if $userPrefs.allowMsgs eq 'y'}checked="checked"{/if} />
			{formhelp note=""}
		{/forminput}
	</div>

	{* the header_inc.tpl file needs some better js for this to work - xing
	<div class="row">
		{formlabel label="Message Alert" for="message_alert"}
		{forminput}
			<input type="checkbox" name="message_alert" id="message_alert" {if $userPrefs.message_alert eq 'y'}checked="checked"{/if} />
			{formhelp note="Whenever you have new messages, a popup will apear and take you directly to your message box."}
		{/forminput}
	</div>
	*}

	<div class="row">
		{formlabel label="Send an email" for="minPrio"}
		{forminput}
			<select name="minPrio" id="minPrio">
				<option value="1" {if $userPrefs.minPrio eq 1}selected="selected"{/if}>{tr}at least priority:{/tr} 1</option>
				<option value="2" {if $userPrefs.minPrio eq 2}selected="selected"{/if}>{tr}at least priority:{/tr} 2</option>
				<option value="3" {if $userPrefs.minPrio eq 3}selected="selected"{/if}>{tr}at least priority:{/tr} 3</option>
				<option value="4" {if $userPrefs.minPrio eq 4}selected="selected"{/if}>{tr}at least priority:{/tr} 4</option>
				<option value="5" {if $userPrefs.minPrio eq 5}selected="selected"{/if}>{tr}at least priority:{/tr} 5</option>
				<option value="6" {if $userPrefs.minPrio eq 6}selected="selected"{/if}>{tr}never send message{/tr}</option>
			</select>
			{formhelp note="Here you can indicate when an email should be sent to you when you recieve a personal message."}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="messprefs" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
