<?php require '_top.php';[$pg,$off,$per]=page(0);$tot=q('SELECT COUNT(*) c FROM emergency_access_logs')->fetch()['c'];
$rows=q("SELECT l.*,u.name FROM emergency_access_logs l JOIN qr_codes c ON c.id=l.qr_id LEFT JOIN users u ON u.id=c.user_id ORDER BY l.id DESC LIMIT $per OFFSET $off")->fetchAll();?>
<h3>Emergency Scan Logs</h3><div class="table-responsive card p-2"><table class="table mb-0"><tr><th>Patient</th><th>Time</th><th>IP</th><th>Device</th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['name'])?></td><td><?=e($r['accessed_at'])?></td><td><?=e($r['ip_address'])?></td><td class="small"><?=e(substr($r['user_agent'],0,70))?></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
