<?php require 'includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $n=trim($_POST['name']??'');$em=strtolower(trim($_POST['email']??''));$ph=trim($_POST['phone']??'');$pw=$_POST['password']??'';
 if(!$n||!$em||!$pw)flash('All required fields must be filled.','danger');
 elseif(!filter_var($em,FILTER_VALIDATE_EMAIL))flash('Invalid email address.','danger');
 elseif(strlen($pw)<8)flash('Password must be at least 8 characters.','danger');
 elseif($pw!==($_POST['confirm']??''))flash('Passwords do not match.','danger');
 elseif(q('SELECT 1 FROM users WHERE email=?',[$em])->fetch())flash('Email already registered.','danger');
 else{q("INSERT INTO users(name,email,password,phone,role) VALUES(?,?,?,?, 'user')",[$n,$em,password_hash($pw,PASSWORD_DEFAULT),$ph]);flash('Registration successful. Please login.');redirect('login.php');}
}
$title='Register'; require 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4"><h3>Create account</h3>
<form method="post"><?=csrf()?>
<div class="mb-2"><label class="form-label">Full Name</label><input name="name" class="form-control" required value="<?=e($_POST['name']??'')?>"></div>
<div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?=e($_POST['email']??'')?>"></div>
<div class="mb-2"><label class="form-label">Phone Number</label><input name="phone" class="form-control" value="<?=e($_POST['phone']??'')?>"></div>
<div class="mb-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm" class="form-control" required></div>
<button class="btn btn-primary w-100">Register</button></form></div></div></div>
<?php require 'includes/footer.php';
