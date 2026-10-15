<?php require '_top.php'; need_super(); require_once __DIR__.'/../includes/content.php';
$id=(int)($_GET['id']??0);$row=$id?q('SELECT * FROM bracelets WHERE id=?',[$id])->fetch():null;
if($id&&!$row){echo '<div class="alert alert-danger">Bracelet not found.</div>';require '_bottom.php';exit;}
$d=$row?:['name'=>'','type'=>'adult','material'=>'Silicone','price'=>'29.90','stock'=>50,'color1'=>'14B8A6','color2'=>'2563EB','description'=>'','is_active'=>1,'image'=>null];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $hex=fn($v,$def)=>preg_match('/^#?[0-9a-f]{6}$/i',$v??'')?strtoupper(ltrim($v,'#')):$def;
 $d=['name'=>trim($_POST['name']??''),'type'=>in_array($_POST['type']??'',['adult','kids'])?$_POST['type']:'adult','material'=>trim($_POST['material']??'Silicone'),'price'=>$_POST['price']??'','stock'=>(int)($_POST['stock']??0),'color1'=>$hex($_POST['color1']??'','14B8A6'),'color2'=>$hex($_POST['color2']??'','2563EB'),'description'=>trim($_POST['description']??''),'is_active'=>isset($_POST['is_active'])?1:0];
 if($d['name']===''||!is_numeric($d['price'])||$d['price']<0||$d['stock']<0)echo '<div class="alert alert-danger">Name, a valid price and stock (0 or more) are required.</div>';
 else{$v=[$d['name'],$d['type'],$d['material'],$d['price'],$d['stock'],$d['color1'],$d['color2'],$d['description'],$d['is_active']];
  $isEdit=(bool)$id;$oldImg=$row['image']??null;
  if($id)q('UPDATE bracelets SET name=?,type=?,material=?,price=?,stock=?,color1=?,color2=?,description=?,is_active=? WHERE id=?',[...$v,$id]);
  else{q('INSERT INTO bracelets(name,type,material,price,stock,color1,color2,description,is_active) VALUES(?,?,?,?,?,?,?,?,?)',$v);$id=(int)db()->lastInsertId();}
  [$img,$imgErr]=save_upload('image','bracelets','b'.$id.'_'.bin2hex(random_bytes(3)));
  $drop=function($f){if($f)@unlink(img_dir('bracelets').'/'.basename($f));};
  if($img){$drop($oldImg);q('UPDATE bracelets SET image=? WHERE id=?',[$img,$id]);}
  elseif(!empty($_POST['remove_image'])){$drop($oldImg);q('UPDATE bracelets SET image=NULL WHERE id=?',[$id]);}
  flash(($isEdit?'Bracelet updated.':'Bracelet added.').($imgErr?' Photo not saved: '.$imgErr:''),$imgErr?'warning':'success');header('Location: bracelets.php');exit;}}?>
<h3><?=$id?'Edit bracelet':'Add bracelet'?></h3><div class="card p-4" style="max-width:720px"><form method="post" class="row g-3" enctype="multipart/form-data"><?=csrf()?>
<div class="col-md-8"><label class="form-label">Name *</label><input name="name" class="form-control" required value="<?=e($d['name'])?>"></div>
<div class="col-md-4"><label class="form-label">Type</label><select name="type" class="form-select"><option value="adult" <?=$d['type']==='adult'?'selected':''?>>Adult</option><option value="kids" <?=$d['type']==='kids'?'selected':''?>>Kids</option></select></div>
<div class="col-md-4"><label class="form-label">Material</label><select name="material" class="form-select"><?php foreach(['Silicone','Stainless steel','Leather','Other'] as $m)echo '<option '.($m===$d['material']?'selected':'').'>'.$m.'</option>';?></select></div>
<div class="col-md-4"><label class="form-label">Price (RM) *</label><input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?=e($d['price'])?>"></div>
<div class="col-md-4"><label class="form-label">Stock *</label><input type="number" min="0" name="stock" class="form-control" required value="<?=e($d['stock'])?>"></div>
<div class="col-md-6"><label class="form-label">Colour 1</label><input type="color" name="color1" class="form-control form-control-color" value="#<?=e($d['color1'])?>"></div>
<div class="col-md-6"><label class="form-label">Colour 2</label><input type="color" name="color2" class="form-control form-control-color" value="#<?=e($d['color2'])?>"></div>
<div class="col-12"><label class="form-label">Photo (from your computer)</label><div class="upl"><div class="th"><?=bracelet_art($d,60)?></div><div class="flex-grow-1"><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="small text-muted mt-1">JPG, PNG or WebP, up to 5 MB. A square photo looks best. Without one, the coloured illustration is used.</div><?php if(!empty($d['image'])):?><div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_image" id="ri"><label class="form-check-label small" for="ri">Remove current photo</label></div><?php endif;?></div></div></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?=e($d['description'])?></textarea></div>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="ia" <?=$d['is_active']?'checked':''?>><label class="form-check-label" for="ia">Visible in the shop</label></div></div>
<div class="col-12"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-primary" href="bracelets.php">Cancel</a></div></form></div>
<?php require '_bottom.php';
