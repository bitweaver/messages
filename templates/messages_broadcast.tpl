<div class="floaticon">{bithelp}</div>
<div class="contain usermessages">
<div class="header">
<h1><a href="{$smarty.const.MESSU_PKG_URL}broadcast.php">{tr}Broadcast message{/tr}</a></h1>
</div>

{include file="bitpackage:users/my_bitweaver_bar.tpl"}
{include file="bitpackage:messu/messages_nav.tpl"}

<div class="body">
{formfeedback error=$errors success=$message}
{if $sent}
	{$message}
{else}
<form action="{$smarty.const.MESSU_PKG_URL}broadcast.php" method="post">
<table class="panel">
  <tr>
    <td><label for="broadcast-group">{tr}Group{/tr}:</label></td>
    <td>
    <select name="group" id="broadcast-group">
    
	{section name=ix loop=$groups}
		{if $groups[ix].group_id && $groups[ix].group_name}
	  		<option value="{$groups[ix].group_id}">{$groups[ix].group_name}</option>
	  	{/if}
	{/section}
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="broadcast-priority">{tr}Priority{/tr}:</label></td><td>
    <select name="priority" id="broadcast-priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1 -Lowest-{/tr}</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2 -Low-{/tr}</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3 -Normal-{/tr}</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4 -High-{/tr}</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5 -Very High-{/tr}</option>
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="broadcast-subject">{tr}Subject{/tr}:</label></td><td><input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="50" maxlength="255"/></td>
  </tr>
  <tr>
    <td><label for="broadcast-body">{tr}Body{/tr}:</label></td><td align="center"><textarea rows="20" cols="50" name="body">{$body|escape}</textarea></td>
  </tr>
  <tr class="panelsubmitrow">
  	<td colspan="2"><input type="submit" name="send" value="{tr}send message{/tr}" /></td>
  </tr>
</table>
</form>
{/if}

</div> {* end .body *}
</div> {* end .messages *}
