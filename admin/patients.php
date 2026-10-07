<?php require '_top.php';[$pg,$off,$per]=page(0);
$tot=q('SELECT COUNT(*) c FROM patient_profiles')->fetch()['c'];
$rows=q("SELECT u.id uid,u.name,p.blood_type,p.allergies,p.medical_conditions,p.emergency_contact_name,p.emergency_contact_phone,p.updated_at FROM patient_profiles p JOIN users u ON u.id=p.user_id ORDER BY p.updated_at DESC LIMIT $per OFFSET $off")->fetchAll();?>
<h3>Patient Profiles</h3><div class="table-responsive card p-2"><table class="table mb-0"><tr><th>Patient</th><th>Blood</th><th>Allergies</th><th>Conditions</th><th>Emergency Contact</th><th>Updated</th></tr>
<?php foreach($rows as $r):?><tr><td><a href="user-view.php?id=<?=$r['uid']?>"><?=e($r['name'])?></a></td><td><?=e($r['blood_type'])?></td><td><?=e($r['allergies'])?></td><td><?=e($r['medical_conditions'])?></td><td><?=e($r['emergency_contact_name'].' '.$r['emergency_contact_phone'])?></td><td><?=e($r['updated_at'])?></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
