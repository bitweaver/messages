{strip}
<div class="display usermessages">
	<div class="header">
		<h1>{tr}Read message{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}
	{include file="bitpackage:messages/messages_nav.tpl"}

	{if $next}
		{assign var=read_id value=$next}
	{elseif $prev}
		{assign var=read_id value=$prev}
	{else}
		{assign var=read_id value=$msg_id}
	{/if}
	<div class="body">
		<ul class="list-inline navbar">
			{if $msg.is_flagged eq 'y'}
				<li>{booticon iname="fa-enevelope-dot" iexplain=Flagged} {smartlink ititle="Unflag Message" offset=$smarty.request.offset act=is_flagged actval=n msg_id=$msg_id sort_mode=$smarty.request.sort_mode find=$smarty.request.find flag=$smarty.request.flag priority=$priority flagval=$smarty.request.flagval}</li>
			{else}
				<li>{smartlink ititle="Flag Message" offset=$smarty.request.offset act=is_flagged actval=y msg_id=$msg_id sort_mode=$smarty.request.sort_mode find=$smarty.request.find flag=$smarty.request.flag priority=$priority flagval=$smarty.request.flagval}</li>
			{/if}
			<li>{smartlink ititle="Delete" msg_id=$read_id offset=$smarty.request.offset msgdel=$msg_id sort_mode=$smarty.request.sort_mode find=$smarty.request.find flag=$smarty.request.flag priority=$priority flagval=$smarty.request.flagval}</li>
			{if $prev}<li>{smartlink ianchor=top ititle="Previous message" booticon="fa-arrow-left" sort_mode=$smarty.request.sort_mode msg_id=$prev find=$smarty.request.find flag=$smarty.request.flag priority=$priority flagval=$smarty.request.flagval}</li>{/if}
			{if $next}<li>{smartlink ianchor=top ititle="Next message" booticon="fa-arrow-right" sort_mode=$smarty.request.sort_mode msg_id=$next find=$smarty.request.find flag=$smarty.request.flag priority=$priority flagval=$smarty.request.flagval}</li>{/if}
		</ul>

		{if $legend}
			{$legend}
		{else}
			{form legend="Message" ipackage=messages ifile='compose.php'}
				<div class="form-group">
					{formlabel label="Date"}
					{forminput}
						{$msg.msg_date|bit_long_datetime}
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="From"}
					{forminput}
						{displayname user_id=$msg.from_user_id}
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="To"}
					{forminput}
						{$msg.msg_to|default:"&nbsp;"}
					{/forminput}
				</div>

				{if $msg.msg_cc}
					<div class="form-group">
						{formlabel label="Carbon Copy"}
						{forminput}
							{$msg.msg_cc}
						{/forminput}
					</div>
				{/if}

				<div class="form-group">
					{formlabel label="Subject"}
					{forminput}
						{$msg.subject}
					{/forminput}
				</div>

				<div class="form-group">
					{forminput class="message"}
						{$msg.parsed}
					{/forminput}
				</div>

				<input type="hidden" name="offset"     value="{$smarty.request.offset}" />
				<input type="hidden" name="find"       value="{$smarty.request.find|escape}" />
				<input type="hidden" name="sort_mode"  value="{$smarty.request.sort_mode}" />
				<input type="hidden" name="flag"       value="{$smarty.request.flag}" />
				<input type="hidden" name="flagval"    value="{$smarty.request.flagval}" />
				<input type="hidden" name="priority"   value="{$smarty.request.priority}" />
				<input type="hidden" name="msgdel"     value="{$msg.msg_id}" />
				<input type="hidden" name="replyto"    value="{$msg.from_user_id}" />
				<input type="hidden" name="replyallto" value="{$msg.msg_to},{$msg.msg_cc}" />
				<input type="hidden" name="subject"    value="{tr}Re:{/tr} {$msg.subject}" />
				<input type="hidden" name="body"       value="{$msg.body|quoted|escape}" />
				{if $next}
					<input type="hidden" name="msg_id" value="{$next}" />
				{elseif $prev}
					<input type="hidden" name="msg_id" value="{$prev}" />
				{else}
					<input type="hidden" name="msg_id" value="{$msg_id}" />
				{/if}

				<div class="form-group submit">
					<input type="submit" class="btn btn-default" name="action[reply]" value="{tr}Reply{/tr}" />&nbsp;
					<input type="submit" class="btn btn-default" name="action[replyall]" value="{tr}Reply All{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .usermessages -->
{/strip}
