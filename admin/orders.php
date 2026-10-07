<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'){$st=$_POST['status']??'';$id=(int)$_POST['id'];
 if(in_array($st,['pending','paid','cancelled'])){$o=q('SELECT * FROM orders WHERE id=?',[$id])->fetch();
  if($o){q('UPDATE orders SET payment_status=?,qr_revealed=? WHERE id=?',[$st,$st==='paid'?1:0,$id]);if($st==='paid')assign_qr($o['user_id']);flash('Order updated.');}}
 header('Location: orders.php');exit;}
[$pg,$off,$per]=page(0);$tot=q('SELECT COUNT(*) c FROM orders')->fetch()['c'];
$rows=q("SELECT o.*,u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT $per OFFSET $off")->fetchAll();?>
<h3>Orders</h3><div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th>Order #</th><th>Customer</th><th>Product</th><th>Amount</th><th>Payment</th><th>QR</th><th>Date</th><th>Update</th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['order_number'])?></td><td><?=e($r['name'])?></td><td><?=e($r['product_name'])?></td><td>RM<?=number_format($r['amount'],2)?></td><td><?=e($r['payment_status'])?></td><td><?=$r['qr_revealed']?'Revealed':'Hidden'?></td><td><?=e($r['created_at'])?></td>
<td><form method="post" class="d-flex gap-1"><?=csrf()?><input type="hidden" name="id" value="<?=$r['id']?>"><select name="status" class="form-select form-select-sm"><?php foreach(['pending','paid','cancelled'] as $s)echo "<option ".($s===$r['payment_status']?'selected':'').">$s</option>";?></select><button class="btn btn-sm btn-primary">Set</button></form></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
