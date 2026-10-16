<?php require '_top.php'; require_once __DIR__.'/../includes/content.php'; $S=stories();
if($_SERVER['REQUEST_METHOD']==='POST'){$slug=$_POST['slug']??'';
 if(!isset($S[$slug])){flash('Unknown story.','danger');header('Location: stories.php');exit;}
 $rm=function()use($slug){foreach(['jpg','jpeg','png','webp'] as $x)@unlink(img_dir('stories').'/'.$slug.'.'.$x);};
 if(isset($_POST['remove'])){$rm();flash('Photo removed. The illustration is shown again.');}
 else{[$f,$er]=save_upload('image','stories',$slug.'_new');
  if($er)flash($er,'danger');elseif(!$f)flash('Choose an image first.','warning');
  else{$rm();rename(img_dir('stories').'/'.$f,img_dir('stories').'/'.$slug.'.'.pathinfo($f,PATHINFO_EXTENSION));flash('Photo saved for '.$S[$slug]['name'].'.');}}
 header('Location: stories.php');exit;}?>
<div class="page-head"><div><h3 class="mb-0">Story photos</h3><span class="text-muted">Upload a photo from your computer for each story. Without one, the illustration is shown.</span></div></div>
<div class="row g-3"><?php foreach($S as $slug=>$s):$has=story_img($slug);?><div class="col-md-6"><div class="card p-3"><div class="upl"><div class="th" style="width:110px;height:110px;background:#<?=$s['c'][3]?>"><?=story_art($slug,$s,90)?></div>
<div class="flex-grow-1"><b><?=e($s['name'])?></b> <span class="tag"><?=e($s['cond'])?></span><form method="post" enctype="multipart/form-data" class="mt-2"><?=csrf()?><input type="hidden" name="slug" value="<?=e($slug)?>"><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm mb-2" required>
<div class="d-flex gap-2"><button class="btn btn-primary btn-sm">Save photo</button><?php if($has):?><button name="remove" value="1" formnovalidate class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this photo?')">Remove</button><?php endif;?></div></form></div></div></div></div><?php endforeach;?></div>
<p class="small text-muted mt-3">Prefer files? Copy a JPG, PNG or WebP into <code>assets/img/stories/</code> named after the story, for example <code><?=e(array_key_first($S))?>.jpg</code>.</p>
<?php require '_bottom.php';
