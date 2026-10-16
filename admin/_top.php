<?php require_once __DIR__.'/../includes/bootstrap.php'; require_admin(); $u=user(); $super=$u['role']==='superadmin';
// Owner: catalog + staff, NO customer chat. Admin: customers + chat with owner.
$nav=['index'=>'Dashboard','users'=>'Users','patients'=>'Patient Profiles','orders'=>'Orders','appointments'=>'Appointments','qr-codes'=>'QR Codes','scan-logs'=>'Emergency Scan Logs'];
if($super)$nav=['index'=>'Dashboard','bracelets'=>'Bracelets','staff'=>'Staff (admins)','staff-chat'=>'Staff chat']+array_slice($nav,1);
else{$nav['messages']='Customer messages';$nav['staff-chat']='Chat with owner';}
$nav['stories']='Story photos';
$cur=basename($_SERVER['SCRIPT_NAME'],'.php');$cur=['user-view'=>'users','user-form'=>'users','bracelet-form'=>'bracelets','staff-form'=>'staff'][$cur]??$cur;
function need_super(){global $super;if(!$super){http_response_code(403);echo '<div class="alert alert-danger">Only the owner can access this page.</div>';require __DIR__.'/_bottom.php';exit;}}
function page($rows,$per=15){$p=max(1,(int)($_GET['page']??1));return [$p,($p-1)*$per,$per];}
function pager($total,$per=15){$p=max(1,(int)($_GET['page']??1));$n=max(1,ceil($total/$per));if($n<2)return;$qs=$_GET;echo '<nav><ul class="pagination pagination-sm">';for($i=1;$i<=$n;$i++){$qs['page']=$i;echo '<li class="page-item '.($i==$p?'active':'').'"><a class="page-link" href="?'.e(http_build_query($qs)).'">'.$i.'</a></li>';}echo '</ul></nav>';}
$title=$super?'Owner':'Admin'; require __DIR__.'/../includes/header.php'; ?>
<div class="row"><div class="col-lg-2 mb-3"><?php if($super):?><span class="badge mb-2" style="background:var(--teal);color:var(--ink)"><i class="bi bi-star-fill"></i> Owner</span><?php endif;?><div class="list-group">
<?php foreach($nav as $f=>$l):?><a class="list-group-item list-group-item-action <?=$cur===$f?'active':''?>" href="<?=BASE_URL?>/admin/<?=$f?>.php"><?=$l?><?php if($f==='messages'):?><span class="hk-badge" style="display:none">0</span><?php elseif($f==='staff-chat'):?><span class="hk-badge" data-b="staff" style="display:none">0</span><?php endif;?></a><?php endforeach;?>
<a class="list-group-item list-group-item-action text-danger" href="<?=BASE_URL?>/logout.php">Logout</a></div></div><div class="col-lg-10">
