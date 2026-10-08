<?php require '_top.php';
$c=fn($s)=>q($s)->fetch()['c'];
$st=['Total Users'=>$c("SELECT COUNT(*) c FROM users WHERE role='user'"),'Total Patients'=>$c('SELECT COUNT(*) c FROM patient_profiles'),'Total Orders'=>$c('SELECT COUNT(*) c FROM orders'),'Paid Orders'=>$c("SELECT COUNT(*) c FROM orders WHERE payment_status='paid'"),'Active QR Codes'=>$c("SELECT COUNT(*) c FROM qr_codes WHERE status='active'"),'QR Scans'=>$c('SELECT COUNT(*) c FROM emergency_access_logs')];?>
<div class="page-head"><h3 class="mb-0">Dashboard</h3></div><div class="row g-3"><?php foreach($st as $l=>$v):?><div class="col-6 col-md-4"><div class="card p-4"><div class="text-muted fw-semibold small"><?=$l?></div><div class="stat"><?=$v?></div></div></div><?php endforeach;?></div>
<?php require '_bottom.php';
