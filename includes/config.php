<?php
// Safe to commit: contains NO secrets. Real values go in includes/config.local.php (git-ignored).
if(file_exists(__DIR__.'/config.local.php'))require __DIR__.'/config.local.php';
$d=['DB_HOST'=>'localhost','DB_NAME'=>'healthkey','DB_USER'=>'root','DB_PASS'=>'',
 'BASE_URL'=>'/healthkey','PRODUCT_NAME'=>'HEALTHKEY Bracelet','PRODUCT_PRICE'=>29.90,
 'GEMINI_API_KEY'=>'',            // free key: https://aistudio.google.com/apikey
 'AI_MODEL'=>'gemini-3.8-flash','AI_FALLBACK_MODEL'=>'gemini-3.1-flash-lite',  // used if the main model is overloaded; '' to disable
'AI_HOURLY_LIMIT'=>30,
 'AI_DEBUG'=>false];   // true = show the real AI error in the chat (turn off before presenting)
foreach($d as $k=>$v)defined($k)||define($k,$v);
