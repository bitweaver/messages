{tr}Hi,
A new message was posted to you. To respond, please visit http://{$smarty.server.HTTP_HOST}{$smarty.const.MESSU_PKG_URL}message_box.php

From: {$mail_from}
Subject: {$mail_subject}
Date:{/tr} {$mail_date|date_format:"%a %b %Y [%H:%I]"}

{$mail_body}