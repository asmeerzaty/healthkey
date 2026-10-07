<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'&&trim($_POST['message']??'')){q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'admin',?)",[(int)$_POST['uid'],trim($_POST['message'])]);flash('Reply sent.');header('Location: messages.php?uid='.(int)$_POST['uid']);exit;}
$users=q("SELECT u.id,u.name,MAX(m.id) last FROM chat_messages m JOIN users u ON u.id=m.user_id WHERE m.sender='user' GROUP BY u.id ORDER BY last DESC")->fetchAll();
$uid=(int)($_GET['uid']??($users[0]['id']??0));$msgs=$uid?q('SELECT * FROM chat_messages WHERE user_id=? ORDER BY id',[$uid])->fetchAll():[];?>
<h3>Chat / Messages</h3><div class="row"><div class="col-md-4 mb-3"><div class="list-group"><?php foreach($users as $x):?><a class="list-group-item <?=$x['id']==$uid?'active':''?>" href="?uid=<?=$x['id']?>"><?=e($x['name'])?></a><?php endforeach;if(!$users)echo '<div class="text-muted">No messages yet.</div>';?></div></div>
<div class="col-md-8"><?php if($uid):?><div class="card p-3"><div class="chat mb-3"><?php foreach($msgs as $m):?><div class="bubble b-<?=$m['sender']?>"><small class="text-muted"><?=e(ucfirst($m['sender']))?> · <?=e($m['created_at'])?></small><br><?=e($m['message'])?></div><?php endforeach;?></div>
<form method="post" class="d-flex gap-2"><?=csrf()?><input type="hidden" name="uid" value="<?=$uid?>"><input name="message" class="form-control" placeholder="Reply as admin" required><button class="btn btn-primary">Send</button></form></div><?php endif;?></div></div>
<?php require '_bottom.php';
