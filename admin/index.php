<?php require '_top.php';
$c=fn($s)=>q($s)->fetch()['c'];
$st=['Total Users'=>$c("SELECT COUNT(*) c FROM users WHERE role='user'"),'Total Patients'=>$c('SELECT COUNT(*) c FROM patient_profiles'),'Total Orders'=>$c('SELECT COUNT(*) c FROM orders'),'Paid Orders'=>$c("SELECT COUNT(*) c FROM orders WHERE payment_status='paid'"),'Active QR Codes'=>$c("SELECT COUNT(*) c FROM qr_codes WHERE status='active'"),'QR Scans'=>$c('SELECT COUNT(*) c FROM emergency_access_logs')];?>
<h3>Dashboard</h3><div class="row g-3"><?php foreach($st as $l=>$v):?><div class="col-6 col-md-4"><div class="card p-3"><div class="text-muted"><?=$l?></div><div class="stat"><?=$v?></div></div></div><?php endforeach;?></div>
<?php require '_bottom.php';
