{strip}
<div class="floaticon">{bithelp}</div>
<div class="listing usermessages">
	<div class="header">
		<h1>{tr}Messages{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}

	<div class="body">
		{form legend="Your Personal Messages"}
			<input type="hidden" name="offset" value="{$offset|escape}" />
			<input type="hidden" name="find" value="{$find|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
			<input type="hidden" name="flag" value="{$flag|escape}" />
			<input type="hidden" name="flagval" value="{$flagval|escape}" />
			<input type="hidden" name="priority" value="{$priority|escape}" />

			<a class="floaticon" href="{$smarty.const.MESSU_PKG_URL}compose.php">{biticon ipackage=messu iname=send_mail iexplain="{tr}Compose Message{/tr}"}</a>
{assign var=displayName value=$gBitSystem->getPreference("display_name","real_name") }
			<table class="data">
				<tr>
					<th style="width:1%">&nbsp;</th>
					<th style="width:1%">{smartlink ititle="Flagged" isort=is_flagged ibiticon="messu/flagged" find=$find flag=$flag offset=$offset priority=$priority flagval=$flagval}</th>
					<th>{smartlink ititle="From" isort=$displayName find=$find flag=$flag offset=$offset priority=$priority flagval=$flagval}</th>
					<th>{smartlink ititle="Subject" isort=subject find=$find flag=$flag offset=$offset priority=$priority flagval=$flagval}</th>
					<th>{smartlink ititle="Date" isort=date find=$find flag=$flag offset=$offset priority=$priority flagval=$flagval}</th>
					<th>{tr}Size{/tr}</th>
				</tr>

				{section name=user loop=$items}
					<tr class="{cycle values="odd,even"} prio{$items[user].priority}{if $items[user].is_read eq 'n'} highlight{/if}">
						<td><input type="checkbox" name="msg[{$items[user].msg_id}]" /></td>
						<td>{if $items[user].is_flagged eq 'y'}{biticon ipackage=messu iname=flagged iexplain="Flagged"}{/if}</td>
						<td>{displayname hash=$items[user]}</td>
						<td><a href="{$smarty.const.MESSU_PKG_URL}read.php?offset={$offset}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;msg_id={$items[user].msg_id}">{$items[user].subject}</a></td>
						<td style="text-align:right;">{$items[user].date|bit_short_datetime}</td>
						<td style="text-align:right;">{$items[user].len|kbsize}</td>
					</tr>
				{sectionelse}
					<tr class="norecords"><td colspan="6">{tr}No messages to display{/tr}</td></tr>
				{/section}
			</table>

			{if $items}
				{tr}Checked items:{/tr}<br />
				<input type="submit" name="delete" value="{tr}Delete{/tr}" />
				&nbsp;{tr}or{/tr}&nbsp;
				<select name="action">
					<option value="is_read_n">{tr}Mark as unread{/tr}</option>
					<option value="is_read_y">{tr}Mark as read{/tr}</option>
					<option value="is_flagged_n">{tr}Unflag{/tr}</option>
					<option value="is_flagged_y">{tr}Flag{/tr}</option>
				</select>
				<input type="submit" name="mark" value="{tr}Mark{/tr}" />
			{/if}
		{/form}

		{form legend="Search your Personal Messages"}
			<div class="row">
				{formlabel label="Messages" for="messages"}
				{forminput}
					<select name="flags" id="messages">
						<option value="is_read_y" {if $flag eq 'is_read' and $flagval eq 'y'}selected="selected"{/if}>{tr}Read{/tr}</option>
						<option value="is_read_n" {if $flag eq 'is_read' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unread{/tr}</option>
						<option value="is_flagged_y" {if $flag eq 'is_flagged' and $flagval eq 'y'}selected="selected"{/if}>{tr}Flagged{/tr}</option>
						<option value="is_flagged_y" {if $flag eq 'isflagged' and $flagval eq 'n'}selected="selected"{/if}>{tr}Unflagged{/tr}</option>
						<option value="" {if $flag eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Priority" for="priority"}
				{forminput}
					<select name="priority" id="priority">
						<option value="" {if $priority eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
						<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
						<option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Containing" for="find"}
				{forminput}
					<input type="text" name="find" size="40" id="find" value="{$find|escape}" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="filter" value="{tr}Filter{/tr}" />
			</div>
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .usermessages -->
{/strip}
