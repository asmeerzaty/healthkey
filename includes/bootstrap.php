<?php
if(session_status()===PHP_SESSION_NONE){session_start();}
if(!ob_get_level())ob_start(); // lets pages redirect after the header HTML was included
require_once __DIR__.'/config.php';require_once __DIR__.'/database.php';require_once __DIR__.'/functions.php';require_once __DIR__.'/auth.php';
check_csrf();
