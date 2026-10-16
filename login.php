<?php require 'includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $em=strtolower(trim($_POST['email']??''));$ip=$_SERVER['REMOTE_ADDR']??'';$locked=false;
 try{$locked=q("SELECT COUNT(*) c FROM login_attempts WHERE attempted_at>DATE_SUB(NOW(),INTERVAL 15 MINUTE) AND (ip=? OR email=?)",[$ip,$em])->fetch()['c']>=8;}catch(Throwable $x){} // table missing = run database/migration_v2.sql
 if($locked)flash('Too many failed attempts. Please wait 15 minutes and try again.','danger');
 else{$u=q('SELECT * FROM users WHERE email=? AND is_active=1',[$em])->fetch();
  if($u&&password_verify($_POST['password']??'',$u['password'])){try{q('DELETE FROM login_attempts WHERE ip=? OR email=?',[$ip,$em]);}catch(Throwable $x){}session_regenerate_id(true);$_SESSION['uid']=$u['id'];redirect(in_array($u['role'],['admin','superadmin'])?'admin/':'dashboard.php');}
  try{q('INSERT INTO login_attempts(ip,email) VALUES(?,?)',[$ip,$em]);}catch(Throwable $x){}
  flash('Invalid email or password.','danger');}
}
$title='Login'; require 'includes/header.php'; ?>
<div class="auth"><div class="card"><h3>Login</h3>
<form method="post"><?=csrf()?><div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-primary w-100">Login</button></form>
<p class="small text-muted mt-3 mb-0">Demo: user@healthkey.test / User123! &nbsp;|&nbsp; admin@healthkey.test / Admin123!</p></div></div>
<?php require 'includes/footer.php';
