<?php
// Force German index.html to load
// This prevents automatic language detection redirects

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');

// Include the German index.html
readfile('index.html');
exit;
?>