<?php require 'includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $u=q('SELECT * FROM users WHERE email=? AND is_active=1',[strtolower(trim($_POST['email']??''))])->fetch();
 if($u&&password_verify($_POST['password']??'',$u['password'])){session_regenerate_id(true);$_SESSION['uid']=$u['id'];redirect($u['role']==='admin'?'admin/':'dashboard.php');}
 flash('Invalid email or password.','danger');
}
$title='Login'; require 'includes/header.php'; ?>
<div class="auth"><div class="card"><h3>Login</h3>
<form method="post"><?=csrf()?><div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-primary w-100">Login</button></form>
<p class="small text-muted mt-3 mb-0">Demo: user@healthkey.test / User123! &nbsp;|&nbsp; admin@healthkey.test / Admin123!</p></div></div>
<?php require 'includes/footer.php';
