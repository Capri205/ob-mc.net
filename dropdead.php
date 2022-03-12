<?php
ignore_user_abort(true);
ob_start();
$size = ob_get_length();
header("Content-Length: $size");
header('Connection: close');
ob_end_flush();
ob_flush();
flush();
if (session_id()) session_write_close();
die();
?>
