<?php $title='Contact admin'; require 'includes/header.php'; require_login();
if($u['role']==='admin')redirect('admin/messages.php');
$msgs=q("SELECT id,sender,message FROM chat_messages WHERE user_id=? AND channel='admin' ORDER BY id",[$u['id']])->fetchAll();
q("UPDATE chat_messages SET is_read=1 WHERE user_id=? AND channel='admin' AND sender='admin'",[$u['id']]);
$lastAdmin=0;foreach($msgs as $m)if($m['sender']==='admin')$lastAdmin=max($lastAdmin,$m['id']);?>
<div class="chat-tabs"><a href="chatbot.php"><i class="bi bi-stars"></i> AI Assistant</a><a class="on" href="support.php"><i class="bi bi-person-headset"></i> Contact admin</a></div>
<div class="row justify-content-center"><div class="col-lg-9"><div class="card chatbox"><div class="p-3 border-bottom d-flex align-items-center gap-2"><span class="brand-dot text-white"><i class="bi bi-person-headset"></i></span><div><b>Chat with admin</b><div class="small text-muted">A real person reads this — no AI here. Replies appear in this chat.</div></div></div>
<div id="msgs" class="msgs"></div>
<form id="f" class="inputbar"><input id="m" class="form-control" maxlength="1000" placeholder="Write a message to the admin…" autocomplete="off" required><button class="btn btn-primary" id="s"><i class="bi bi-send-fill"></i></button></form>
<div class="disc">Do not use this for emergencies — call 999 / 112.</div></div></div></div>
<script>const HK={base:<?=json_encode(BASE_URL)?>,csrf:<?=json_encode($_SESSION['csrf']??'')?>};</script>
<script src="<?=BASE_URL?>/assets/js/chat.js"></script>
<script>
const box=document.getElementById('msgs'),HIST=<?=json_encode($msgs)?>;let last=<?=$lastAdmin?>;
HIST.forEach(h=>addMsg(box,h.sender,h.message));if(!HIST.length)addMsg(box,'admin','Hello! Send us your question and an admin will reply here.');
document.getElementById('f').onsubmit=async e=>{e.preventDefault();const i=document.getElementById('m'),t=i.value.trim();if(!t)return;i.value='';addMsg(box,'user',t,'Sent to admin');
 const fd=new FormData();fd.append('csrf',HK.csrf);fd.append('channel','admin');fd.append('message',t);try{await post(fd)}catch(x){addMsg(box,'admin','Could not send — please try again.')}};
setInterval(async()=>{try{const r=await (await fetch(HK.base+'/chat-api.php?action=poll&channel=admin&after='+last)).json();(r.messages||[]).forEach(m=>{addMsg(box,'admin',m.message);last=Math.max(last,+m.id)})}catch(e){}},6000);
</script><?php require 'includes/footer.php';
