<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'&&trim($_POST['message']??'')){
 q("INSERT INTO chat_messages(user_id,sender,channel,message,is_read) VALUES(?, 'admin','admin',?,0)",[(int)$_POST['uid'],trim($_POST['message'])]);
 header('Location: messages.php?uid='.(int)$_POST['uid']);exit;}
// Only the 'admin' channel — AI conversations never appear here.
$users=q("SELECT u.id,u.name,MAX(m.id) last,MAX(m.created_at) lastat,SUM(m.sender='user' AND m.is_read=0) unread FROM chat_messages m JOIN users u ON u.id=m.user_id WHERE m.channel='admin' GROUP BY u.id,u.name ORDER BY last DESC")->fetchAll();
$uid=(int)($_GET['uid']??($users[0]['id']??0));$msgs=[];
if($uid){q("UPDATE chat_messages SET is_read=1 WHERE user_id=? AND channel='admin' AND sender='user'",[$uid]);
 $msgs=q("SELECT * FROM chat_messages WHERE user_id=? AND channel='admin' ORDER BY id",[$uid])->fetchAll();}
$who=$uid?q('SELECT name,email FROM users WHERE id=?',[$uid])->fetch():null;?>
<h3>Messages <small class="text-muted fs-6">(users who contacted admin)</small></h3>
<div class="row g-3"><div class="col-md-4"><div class="card" style="overflow:hidden"><?php foreach($users as $x):?>
<a class="thread <?=$x['id']==$uid?'on':''?> <?=$x['unread']?'unread':''?>" href="?uid=<?=$x['id']?>"><div class="d-flex justify-content-between"><b><?=e($x['name'])?></b><?php if($x['unread']&&$x['id']!=$uid):?><span class="hk-badge" style="margin:0"><?=$x['unread']?></span><?php endif;?></div><small class="text-muted"><?=e($x['lastat'])?></small></a><?php endforeach;if(!$users)echo '<div class="p-3 text-muted">No messages yet.</div>';?></div></div>
<div class="col-md-8"><?php if($uid&&$who):?><div class="card chatbox" style="height:560px"><div class="p-3 border-bottom"><b><?=e($who['name'])?></b> <span class="text-muted small"><?=e($who['email'])?> · <a href="user-view.php?id=<?=$uid?>">view profile</a></span></div>
<div id="msgs" class="msgs"><?php foreach($msgs as $m):?><div class="bub b-<?=$m['sender']==='admin'?'user':'admin'?>"><small><?=$m['sender']==='admin'?'You (admin)':e($who['name'])?> · <?=e($m['created_at'])?></small><p><?=nl2br(e($m['message']))?></p></div><?php endforeach;?></div>
<form method="post" class="inputbar"><?=csrf()?><input type="hidden" name="uid" value="<?=$uid?>"><input id="reply" name="message" class="form-control" placeholder="Reply to <?=e($who['name'])?>…" required autocomplete="off"><button class="btn btn-primary"><i class="bi bi-send-fill"></i></button></form></div>
<script>document.getElementById('msgs').scrollTop=1e9;setInterval(()=>{if(!document.getElementById('reply').value)location.reload()},15000)</script><?php endif;?></div></div>
<?php require '_bottom.php';
