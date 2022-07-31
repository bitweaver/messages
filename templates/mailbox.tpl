{strip}
<div class="floaticon">{bithelp}</div>
<div class="listing usermessages">
	<div class="header">
		<h1>{tr}Messages{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}
	{include file="bitpackage:messages/messages_nav.tpl"}

	<div class="body">
		{form legend="Your Personal Messages"}
			<input type="hidden" name="find" value="{$smarty.request.find|escape}" />
			<input type="hidden" name="sort_mode" value="{$smarty.request.sort_mode|escape}" />
			<input type="hidden" name="flag" value="{$smarty.request.flag|escape}" />
			<input type="hidden" name="flagval" value="{$smarty.request.flagval|escape}" />
			<input type="hidden" name="priority" value="{$smarty.request.priority|escape}" />

			<a href="{$smarty.const.MESSAGES_PKG_URL}compose.php">{booticon iname="fa-envelope" iexplain="Compose Message"_text}</a>

			{assign var=displayName value=$gBitSystem->getConfig("users_display_name","real_name") }
			<table class="table data">
				<caption>{tr}Messages{/tr}</caption>
				<tr>
					<th style="width:1%">&nbsp;</th>
					<th style="width:1%">{smartlink ititle="Flagged" isort=is_flagged ibiticon="icons/mail-mark-important" find=$smarty.request.find flag=$smarty.request.flag priority=$smarty.request.priority flagval=$smarty.request.flagval}</th>
					<th>{smartlink ititle="From" isort=$displayName find=$smarty.request.find flag=$smarty.request.flag priority=$smarty.request.priority flagval=$smarty.request.flagval}</th>
					<th>{smartlink ititle="Subject" isort=subject find=$smarty.request.find flag=$smarty.request.flag priority=$smarty.request.priority flagval=$smarty.request.flagval}</th>
					<th>{smartlink ititle="Date" isort=msg_date find=$smarty.request.find flag=$smarty.request.flag priority=$smarty.request.priority flagval=$smarty.request.flagval}</th>
					<th>{tr}Size{/tr}</th>
				</tr>

				{section name=user loop=$items}
					<tr class="{cycle values="odd,even"}{if $items[user].is_read eq 'n'} highlight{/if}">
						<td><input type="checkbox" name="msg[{$items[user].msg_id}]" /></td>
						<td class="prio{$items[user].priority}">{if $items[user].is_flagged eq 'y'}{biticon ipackage="icons" iname="mail-mark-important" iexplain="Flagged"}{/if}</td>
						<td>{displayname hash=$items[user]}</td>
						<td>
							<a href="{$smarty.const.MESSAGES_PKG_URL}read.php?flag={$smarty.request.flag}&amp;priority={$smarty.request.priority}&amp;flagval={$smarty.request.flagval}&amp;sort_mode={$smarty.request.sort_mode}&amp;find={$smarty.request.find}&amp;msg_id={$items[user].msg_id}">{$items[user].subject}</a>
							{if $items[user].is_broadcast_message} <small>[{tr}broadcast{/tr}]</small>{/if}
						</td>
						<td style="text-align:right;">{$items[user].msg_date|bit_short_datetime}</td>
						<td style="text-align:right;">{$items[user].len|display_bytes}</td>
					</tr>
				{sectionelse}
					<tr class="norecords"><td colspan="6">{tr}No messages to display{/tr}</td></tr>
				{/section}
			</table>

			{if $items}
				{tr}Checked items:{/tr}<br />
				<input type="submit" class="btn btn-default" name="delete" value="{tr}Delete{/tr}" />
				&nbsp;{tr}or{/tr}&nbsp;
				<select name="action">
					<option value="is_read_n">{tr}Mark as unread{/tr}</option>
					<option value="is_read_y">{tr}Mark as read{/tr}</option>
					<option value="is_flagged_n">{tr}Unflag{/tr}</option>
					<option value="is_flagged_y">{tr}Flag{/tr}</option>
				</select>
				<input type="submit" class="btn btn-default" name="mark" value="{tr}Mark{/tr}" />
			{/if}
		{/form}

		{form legend="Search your Personal Messages"}
			<div class="form-group">
				{formlabel label="Messages" for="messages"}
				{forminput}
					<select name="flags" id="messages">
						<option value="">{tr}All{/tr}</option>
						<option value="is_read_y"    {if $smarty.request.flag eq 'is_read'    and $smarty.request.flagval eq 'y'}selected="selected"{/if}>{tr}Read{/tr}</option>
						<option value="is_read_n"    {if $smarty.request.flag eq 'is_read'    and $smarty.request.flagval eq 'n'}selected="selected"{/if}>{tr}Unread{/tr}</option>
						<option value="is_flagged_y" {if $smarty.request.flag eq 'is_flagged' and $smarty.request.flagval eq 'y'}selected="selected"{/if}>{tr}Flagged{/tr}</option>
						<option value="is_flagged_y" {if $smarty.request.flag eq 'isflagged'  and $smarty.request.flagval eq 'n'}selected="selected"{/if}>{tr}Unflagged{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Priority" for="priority"}
				{forminput}
					<select name="priority" id="priority">
						<option value=""  {if $smarty.request.priority eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
						<option value="1" {if $smarty.request.priority eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
						<option value="2" {if $smarty.request.priority eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
						<option value="3" {if $smarty.request.priority eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
						<option value="4" {if $smarty.request.priority eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
						<option value="5" {if $smarty.request.priority eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Containing" for="find"}
				{forminput}
					<input type="text" name="find" size="40" id="find" value="{$smarty.request.find|escape}" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="filter" value="{tr}Filter{/tr}" />
			</div>
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .usermessages -->
{/strip}
