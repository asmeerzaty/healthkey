<?php $title='Account'; require 'includes/header.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){q('UPDATE users SET name=?,phone=? WHERE id=?',[trim($_POST['name']),trim($_POST['phone']),$u['id']]);
 if(!empty($_POST['password'])){if(strlen($_POST['password'])<8){flash('Password must be at least 8 characters.','danger');redirect('profile.php');}q('UPDATE users SET password=? WHERE id=?',[password_hash($_POST['password'],PASSWORD_DEFAULT),$u['id']]);}
 flash('Account updated.');redirect('profile.php');}?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4"><h3>Account Settings</h3><form method="post"><?=csrf()?>
<label class="form-label">Name</label><input name="name" class="form-control mb-2" value="<?=e($u['name'])?>" required>
<label class="form-label">Email</label><input class="form-control mb-2" value="<?=e($u['email'])?>" disabled>
<label class="form-label">Phone</label><input name="phone" class="form-control mb-2" value="<?=e($u['phone'])?>">
<label class="form-label">New password (optional)</label><input type="password" name="password" class="form-control mb-3"><button class="btn btn-primary">Save</button></form></div></div></div>
<?php require 'includes/footer.php';
