{strip}
<div class="display usermessages">
	<div class="header">
		<h1>{tr}Read message{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}

	{if $next}
		{assign var=read_id value=$next}
	{elseif $prev}
		{assign var=read_id value=$prev}
	{else}
		{assign var=read_id value=$msg_id}
	{/if}
	<div class="body">
		<div class="navbar">
			<ul>
				{if $msg.is_flagged eq 'y'}
					<li>{biticon ipackage=messu iname=flagged iexplain=Flagged} {smartlink ititle="Unflag Message" offset=$offset act=is_flagged actval=n msg_id=$msg_id sort_mode=$sort_mode find=$find flag=$flag priority=$priority flagval=$flagval}</li>
				{else}
					<li>{smartlink ititle="Flag Message" offset=$offset act=is_flagged actval=y msg_id=$msg_id sort_mode=$sort_mode find=$find flag=$flag priority=$priority flagval=$flagval}</li>
				{/if}
				<li>{smartlink ititle="Delete" msg_id=$read_id offset=$offset msgdel=$msg_id sort_mode=$sort_mode find=$find flag=$flag priority=$priority flagval=$flagval}</li>
				{if $prev}<li>{smartlink ianchor=top ititle="Previous message" ibiticon="liberty/nav_prev" sort_mode=$sort_mode msg_id=$prev find=$find flag=$flag priority=$priority flagval=$flagval}</li>{/if}
				{if $next}<li>{smartlink ianchor=top ititle="Next message" ibiticon="liberty/nav_next" sort_mode=$sort_mode msg_id=$next find=$find flag=$flag priority=$priority flagval=$flagval}</li>{/if}
			</ul>
		</div>

		{if $legend}
			{$legend}
		{else}
			{form legend="Message" ipackage=messu ifile='compose.php'}
				<div class="row">
					{formlabel label="Date"}
					{forminput}
						{$msg.date|bit_long_datetime}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="From"}
					{forminput}
						{displayname user_id=$msg.from_user_id}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="To"}
					{forminput}
						{$msg.msg_to}
					{/forminput}
				</div>

				{if $msg.msg_cc}
					<div class="row">
						{formlabel label="Carbon Copy"}
						{forminput}
							{$msg.msg_cc}
						{/forminput}
					</div>
				{/if}

				<div class="row">
					{formlabel label="Subject"}
					{forminput class="highlight"}
						{$msg.subject}
					{/forminput}
				</div>

				<div class="row">
					{forminput}
						{$msg.parsed}
					{/forminput}
				</div>

				<input type="hidden" name="offset" value="{$offset}" />
				<input type="hidden" name="find" value="{$find|escape}" />
				<input type="hidden" name="sort_mode" value="{$sort_mode}" />
				<input type="hidden" name="flag" value="{$flag}" />
				<input type="hidden" name="flagval" value="{$flagval}" />
				<input type="hidden" name="priority" value="{$priority}" />
				<input type="hidden" name="msgdel" value="{$msg_id}" />
				<input type="hidden" name="replyto" value="{$msg.from_user_id}" />
				<input type="hidden" name="replyallto" value="{$msg.msg_to},{$msg.msg_cc}" />
				<input type="hidden" name="subject" value="{tr}Re:{/tr} {$msg.subject}" />
				<input type="hidden" name="body" value="{$msg.body|quoted|escape}" />
				{if $next}
					<input type="hidden" name="msg_id" value="{$next}" />
				{elseif $prev}
					<input type="hidden" name="msg_id" value="{$prev}" />
				{else}
					<input type="hidden" name="msg_id" value="{$msg_id}" />
				{/if}

				<div class="row submit">
					<input type="submit" name="action[reply]" value="{tr}Reply{/tr}" />&nbsp;
					<input type="submit" name="action[replyall]" value="{tr}Reply All{/tr}" />
				</div>
			{/form}

		{/if}
	</div><!-- end .body -->
</div><!-- end .usermessages -->
{/strip}
{*
			<table class="panel">
				<tr class="panelsubmitrow"><td>
					<form method="post" action="{$smarty.const.MESSU_PKG_URL}read.php">
						<input type="hidden" name="offset" value="{$offset}" />
						<input type="hidden" name="find" value="{$find|escape}" />
						<input type="hidden" name="sort_mode" value="{$sort_mode}" />
						<input type="hidden" name="flag" value="{$flag}" />
						<input type="hidden" name="flagval" value="{$flagval}" />
						<input type="hidden" name="priority" value="{$priority}" />
						<input type="hidden" name="msgdel" value="{$msg_id}" />
						{if $next}
							<input type="hidden" name="msg_id" value="{$next}" />
						{elseif $prev}
							<input type="hidden" name="msg_id" value="{$prev}" />
						{else}
							<input type="hidden" name="msg_id" value="" />
						{/if}
						<input type="submit" name="delete" value="{tr}Delete{/tr}" />
					</form>
					</td>
					<td>
					<form method="post" action="{$smarty.const.MESSU_PKG_URL}compose.php">
					<input type="hidden" name="offset" value="{$offset}" />
					<input type="hidden" name="msg_id" value="{$msg_id}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode}" />
					<input type="hidden" name="flag" value="{$flag}" />
					<input type="hidden" name="priority" value="{$priority}" />
					<input type="hidden" name="flagval" value="{$flagval}" />
					<input type="hidden" name="to" value="{$msg.user_from|escape}" />
					<input type="hidden" name="subject" value="{tr}Re:{/tr} {$msg.subject}" />
					<input type="hidden" name="body" value="{$msg.body|quoted|escape}" />
					<input type="submit" name="reply" value="{tr}reply{/tr}" />
					</form>
					</td>
					<td>
					<form method="post" action="{$smarty.const.MESSU_PKG_URL}compose.php">
					<input type="hidden" name="offset" value="{$offset}" />
					<input type="hidden" name="find" value="{$find|escape}" />
					<input type="hidden" name="msg_id" value="{$msg_id}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode}" />
					<input type="hidden" name="flag" value="{$flag}" />
					<input type="hidden" name="priority" value="{$priority}" />
					<input type="hidden" name="flagval" value="{$flagval}" />
					<input type="hidden" name="to" value="{$msg.user_from},{$msg.user_cc},{$msg.user_to}" />
					<input type="hidden" name="subject" value="{tr}Re:{/tr} {$msg.subject}" />
					<input type="hidden" name="body" value="{$msg.body|quoted|escape}" />
					<input type="submit" name="replyall" value="{tr}Reply All{/tr}" />
				</td></tr>
			</table>
*}
