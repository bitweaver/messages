<a class="pagetitle" href="{$gBitLoc.MESSU_PKG_URL}contact.php">{tr}Contact us{/tr}</a><br /><br />
{if $gBitSystem->isFeatureActive( 'feature_messages' ) and $bit_p_messages eq 'y'}
{if $message}
{$message}
{/if}

<h2>{tr}Send a message to us{/tr}</h2>
  <form method="post" action="{$gBitLoc.MESSU_PKG_URL}contact.php">
  <input type="hidden" name="to" value="{$contact_user|escape}" />
  <table class="panel">
  <tr>
    <td>{tr}Priority:{/tr}</td><td>
    <select name="priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1 -Lowest-{/tr}</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2 -Low-{/tr}</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3 -Normal-{/tr}</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4 -High-{/tr}</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5 -Very High-{/tr}</option>
    </select>
    <input type="submit" name="send" value="{tr}send{/tr}" />
    </td>
  </tr>
  <tr>
    <td>{tr}Subject{/tr}:</td><td><input type="text" name="subject" value="" size="80" maxlength="255" /></td>
  </tr>
  <tr><td>&nbsp;</td>
      <td><textarea rows="20" cols="80" name="body"></textarea></td>
  </tr>
</table>
</form>
{/if}
{if strlen($email)>0}
<h2>{tr}Contact us via email{/tr}</h2>                              
{mailto address="$email" encode="javascript" text="click here to send us an email"}
{/if}
