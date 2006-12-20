{tr}Hi,
A new message was posted to you. To respond, please visit http://{$smarty.server.HTTP_HOST}{$smarty.const.MESSAGES_PKG_URL}message_box.php

From: {$from}
Subject: {$msgHash.subject}
Date:{/tr} {$msgHash.msg_date|bit_long_datetime}

{$msgHash.body}
