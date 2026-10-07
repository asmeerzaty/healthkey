<?php
if(session_status()===PHP_SESSION_NONE){session_start();}
require_once __DIR__.'/config.php';require_once __DIR__.'/database.php';require_once __DIR__.'/functions.php';require_once __DIR__.'/auth.php';
check_csrf();
