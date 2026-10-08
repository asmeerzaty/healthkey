<?php require_once __DIR__.'/../includes/bootstrap.php'; require_admin(); $u=user();
$nav=['index'=>'Dashboard','users'=>'Users','patients'=>'Patient Profiles','orders'=>'Orders','qr-codes'=>'QR Codes','scan-logs'=>'Emergency Scan Logs','messages'=>'Chat / Messages'];
$cur=basename($_SERVER['SCRIPT_NAME'],'.php'); if($cur==='user-view')$cur='users';
function page($rows,$per=15){$p=max(1,(int)($_GET['page']??1));return [$p,($p-1)*$per,$per];}
function pager($total,$per=15){$p=max(1,(int)($_GET['page']??1));$n=max(1,ceil($total/$per));if($n<2)return;$qs=$_GET;echo '<nav><ul class="pagination pagination-sm">';for($i=1;$i<=$n;$i++){$qs['page']=$i;echo '<li class="page-item '.($i==$p?'active':'').'"><a class="page-link" href="?'.e(http_build_query($qs)).'">'.$i.'</a></li>';}echo '</ul></nav>';}
$title='Admin'; require __DIR__.'/../includes/header.php'; ?>
<div class="row"><div class="col-lg-2 mb-3"><div class="list-group">
<?php foreach($nav as $f=>$l):?><a class="list-group-item list-group-item-action <?=$cur===$f?'active':''?>" href="<?=BASE_URL?>/admin/<?=$f?>.php"><?=$l?><?php if($f==='messages'):?><span class="hk-badge" style="display:none">0</span><?php endif;?></a><?php endforeach;?>
<a class="list-group-item list-group-item-action text-danger" href="<?=BASE_URL?>/logout.php">Logout</a></div></div><div class="col-lg-10">
