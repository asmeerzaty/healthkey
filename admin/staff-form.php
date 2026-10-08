<?php require '_top.php'; need_super();
$id=(int)($_GET['id']??0);$row=$id?q("SELECT * FROM users WHERE id=? AND role IN('admin','superadmin')",[$id])->fetch():null;
if($id&&!$row){echo '<div class="alert alert-danger">Account not found.</div>';require '_bottom.php';exit;}
$self=$row&&$row['id']==$u['id'];$d=$row?:['name'=>'','email'=>'','phone'=>'','is_active'=>1];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $n=trim($_POST['name']??'');$em=strtolower(trim($_POST['email']??''));$ph=trim($_POST['phone']??'');$pw=$_POST['password']??'';$act=isset($_POST['is_active'])||$self?1:0;
 $d=['name'=>$n,'email'=>$em,'phone'=>$ph,'is_active'=>$act];$err=null;
 if(!$n||!filter_var($em,FILTER_VALIDATE_EMAIL))$err='A name and a valid email are required.';
 elseif(q('SELECT 1 FROM users WHERE email=? AND id<>?',[$em,$id])->fetch())$err='Email already in use.';
 elseif((!$id||$pw!=='')&&strlen($pw)<8)$err='Password must be at least 8 characters.';
 if($err)echo '<div class="alert alert-danger">'.e($err).'</div>';
 else{if($id){q('UPDATE users SET name=?,email=?,phone=?,is_active=? WHERE id=?',[$n,$em,$ph,$act,$id]);if($pw!=='')q('UPDATE users SET password=? WHERE id=?',[password_hash($pw,PASSWORD_DEFAULT),$id]);flash('Account updated.');}
  else{q("INSERT INTO users(name,email,password,phone,role,is_active) VALUES(?,?,?,?,'admin',?)",[$n,$em,password_hash($pw,PASSWORD_DEFAULT),$ph,$act]);flash('Admin created.');}
  header('Location: staff.php');exit;}}?>
<h3><?=$id?'Edit '.($self?'my account':'admin'):'Add admin'?></h3><div class="card p-4" style="max-width:640px"><form method="post" class="row g-3"><?=csrf()?>
<div class="col-12"><label class="form-label">Full name</label><input name="name" class="form-control" required value="<?=e($d['name'])?>"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?=e($d['email'])?>"></div>
<div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?=e($d['phone'])?>"></div>
<div class="col-12"><label class="form-label">Password <?=$id?'<span class="text-muted">(leave blank to keep)</span>':''?></label><input type="password" name="password" class="form-control" <?=$id?'':'required'?>></div>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="ia" <?=$d['is_active']?'checked':''?> <?=$self?'disabled':''?>><label class="form-check-label" for="ia">Account active</label></div></div>
<div class="col-12"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-primary" href="staff.php">Cancel</a></div></form></div>
<?php require '_bottom.php';
