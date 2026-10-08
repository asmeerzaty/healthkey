<?php
// Session auth + role checks + CSRF
function user(){static $u=false;if($u===false){$u=!empty($_SESSION['uid'])?q('SELECT * FROM users WHERE id=? AND is_active=1',[$_SESSION['uid']])->fetch():null;}return $u?:null;}
function require_login(){if(!user())redirect('login.php');}
function is_staff($u=null){$u=$u??user();return $u&&in_array($u['role'],['admin','superadmin']);}
function require_admin(){if(!is_staff()){http_response_code(403);die('You do not have permission to access this page.');}}
function require_super(){$u=user();if(!$u||$u['role']!=='superadmin'){http_response_code(403);die('Only the owner can access this page.');}}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(16));return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';}
function check_csrf(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??''))die('Invalid CSRF token.');}
