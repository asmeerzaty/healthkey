<?php
if(file_exists(__DIR__.'/config.local.php'))require __DIR__.'/config.local.php';
$d=['DB_HOST'=>'localhost','DB_NAME'=>'healthkey','DB_USER'=>'root','DB_PASS'=>'',
 'BASE_URL'=>'/healthkey','PRODUCT_NAME'=>'HEALTHKEY Bracelet','PRODUCT_PRICE'=>29.90,
 'GEMINI_API_KEY'=>'',            // free key: https://aistudio.google.com/apikey
 'AI_MODEL'=>'gemini-2.5-flash','AI_HOURLY_LIMIT'=>30];
foreach($d as $k=>$v)defined($k)||define($k,$v);
