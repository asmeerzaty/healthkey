<?php $title='Chatbot'; require 'includes/header.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'&&trim($_POST['message']??'')){
 $m=trim($_POST['message']);q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'user',?)",[$u['id'],$m]);
 if(($_POST['to']??'bot')==='bot')q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'bot',?)",[$u['id'],bot_reply($m)]);
 else flash('Message sent to admin. A reply will appear here.');
 redirect('chatbot.php');}
$msgs=q('SELECT * FROM chat_messages WHERE user_id=? ORDER BY id',[$u['id']])->fetchAll();?>
<div class="row justify-content-center"><div class="col-md-8"><div class="card p-4"><h3>Chat</h3>
<div class="alert alert-warning py-2 small">HEALTHKEY chatbot provides general information only and is not a substitute for professional medical advice.</div>
<div class="chat mb-3" id="c"><?php if(!$msgs)echo '<p class="text-muted">Say hello or ask a basic question…</p>';foreach($msgs as $m):?><div class="bubble b-<?=$m['sender']?>"><small class="text-muted"><?=e(ucfirst($m['sender']))?></small><br><?=e($m['message'])?></div><?php endforeach;?></div>
<form method="post" class="row g-2"><?=csrf()?><div class="col-12"><div class="btn-group btn-group-sm"><input type="radio" class="btn-check" name="to" id="tb" value="bot" checked><label class="btn btn-outline-primary" for="tb">Ask Chatbot</label><input type="radio" class="btn-check" name="to" id="ta" value="admin"><label class="btn btn-outline-primary" for="ta">Contact Admin</label></div></div>
<div class="col-9"><input name="message" class="form-control" required maxlength="500" placeholder="Type a message"></div><div class="col-3"><button class="btn btn-primary w-100">Send</button></div></form></div></div></div>
<script>c.scrollTop=c.scrollHeight</script><?php require 'includes/footer.php';
