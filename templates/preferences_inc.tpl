{strip}
{form legend="User Messages"}
	<div class="row">
		{formlabel label="Personal Messages" for=""}
		{forminput}
			<a href="{$smarty.const.MESSAGES_PKG_URL}message_box.php">
				{tr}You have <strong>{$unreadMsgs|default:no} unread</strong> {if $unreadMsgs eq '1'}message{else}messages{/if}{/tr}
			</a>
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Messages per page" for="messages_max_records"}
		{forminput}
			<select name="messages_max_records" id="messages_max_records">
				<option value="2"  {if $userPrefs.messages_max_records eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
				<option value="5"  {if $userPrefs.messages_max_records eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
				<option value="10" {if $userPrefs.messages_max_records eq 10}selected="selected"{/if}>{tr}10{/tr}</option>
				<option value="20" {if $userPrefs.messages_max_records eq 20}selected="selected"{/if}>{tr}20{/tr}</option>
				<option value="30" {if $userPrefs.messages_max_records eq 30}selected="selected"{/if}>{tr}30{/tr}</option>
				<option value="40" {if $userPrefs.messages_max_records eq 40}selected="selected"{/if}>{tr}40{/tr}</option>
				<option value="50" {if $userPrefs.messages_max_records eq 50}selected="selected"{/if}>{tr}50{/tr}</option>
			</select>
			{formhelp note=""}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Allow messages from other users" for="messages_allow_messages"}
		{forminput}
			<input type="checkbox" name="messages_allow_messages" id="messages_allow_messages" {if $userPrefs.messages_allow_messages eq 'y'}checked="checked"{/if} />
			{formhelp note=""}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Message Alert" for="messages_alert"}
		{forminput}
			<input type="checkbox" name="messages_alert" id="messages_alert" {if $userPrefs.messages_alert eq 'y'}checked="checked"{/if} />
			{formhelp note="Whenever you have new messages, a popup will apear and take you directly to your message box."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Send an email" for="messages_min_priority"}
		{forminput}
			<select name="messages_min_priority" id="messages_min_priority">
				<option value="1" {if $userPrefs.messages_min_priority eq 1}selected="selected"{/if}>{tr}at least priority:{/tr} 1</option>
				<option value="2" {if $userPrefs.messages_min_priority eq 2}selected="selected"{/if}>{tr}at least priority:{/tr} 2</option>
				<option value="3" {if $userPrefs.messages_min_priority eq 3}selected="selected"{/if}>{tr}at least priority:{/tr} 3</option>
				<option value="4" {if $userPrefs.messages_min_priority eq 4}selected="selected"{/if}>{tr}at least priority:{/tr} 4</option>
				<option value="5" {if $userPrefs.messages_min_priority eq 5}selected="selected"{/if}>{tr}at least priority:{/tr} 5</option>
				<option value="6" {if $userPrefs.messages_min_priority eq 6}selected="selected"{/if}>{tr}never send message{/tr}</option>
			</select>
			{formhelp note="Here you can indicate when an email should be sent to you when you recieve a personal message."}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="messprefs" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
