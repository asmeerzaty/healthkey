<?php $title='AI Assistant'; require 'includes/header.php'; require 'includes/ai.php'; require_login();
$msgs=array_reverse(q("SELECT sender,message FROM chat_messages WHERE user_id=? AND channel='ai' ORDER BY id DESC LIMIT 60",[$u['id']])->fetchAll());?>
<div class="chat-tabs"><a class="on" href="chatbot.php"><i class="bi bi-stars"></i> AI Assistant</a><a href="support.php"><i class="bi bi-person-headset"></i> Contact admin <span class="hk-badge" style="display:none">0</span></a></div>
<div class="chatpage"><div class="chat-side">
<div class="card p-3 mb-3"><div class="d-flex align-items-center gap-2 mb-2"><span class="brand-dot text-white"><i class="bi bi-stars"></i></span><b>HealthKey Assistant</b></div>
<span class="badge <?=ai_ready()?'bg-success':'bg-secondary'?> align-self-start"><?=ai_ready()?'AI mode on':'Basic mode (no API key)'?></span>
<p class="small text-muted mt-2 mb-0">Ask anything health-related — conditions, medicines, lab results, first aid, or how HEALTHKEY works. Need a person? Use <a href="support.php">Contact admin</a>.</p></div>
<div class="card p-3 mb-3"><label class="form-check d-flex gap-2 small"><input type="checkbox" id="useprof" class="form-check-input mt-1"><span><b>Use my health profile</b><br><span class="text-muted">Personalises answers (e.g. allergies). It is sent to Google (free tier) only while this is on; leave off if you prefer privacy.</span></span></label></div>
<div class="card p-3"><button class="btn btn-sm btn-link text-danger" id="clr">Clear conversation</button></div></div>
<div class="card chatbox"><div id="msgs" class="msgs"></div>
<div class="chips-row" id="chips"><?php foreach(['What is hypertension?','Explain how insulin resistance leads to type 2 diabetes','What should I do if someone is having a seizure?','Can I take ibuprofen if I have asthma?','How do I read a cholesterol report?','What is anaphylaxis and how is it treated?'] as $c)echo '<button type="button">'.e($c).'</button>';?></div>
<form id="f" class="inputbar"><input id="m" class="form-control" maxlength="1000" placeholder="Ask a health question…" autocomplete="off" required><button class="btn btn-primary" id="s"><i class="bi bi-send-fill"></i></button></form>
<div class="disc">General information only — not a diagnosis or a substitute for a doctor. In an emergency call 999 / 112.</div></div></div>
<script>const HK={base:<?=json_encode(BASE_URL)?>,csrf:<?=json_encode($_SESSION['csrf']??'')?>};</script>
<script src="<?=BASE_URL?>/assets/js/chat.js"></script>
<script>
const box=document.getElementById('msgs'),HIST=<?=json_encode($msgs)?>;
HIST.forEach(h=>addMsg(box,h.sender,h.message));if(!HIST.length)addMsg(box,'bot','Hi! I can explain medical terms, conditions, medicines, lab results and first aid, or help with HEALTHKEY. What would you like to know?');
async function send(t){if(!t.trim())return;document.getElementById('chips').style.display='none';addMsg(box,'user',t);
 const ty=addMsg(box,'bot','');ty.classList.add('typing');ty.innerHTML='<small>Assistant</small><span class="dots"><i></i><i></i><i></i></span>';
 const fd=new FormData();fd.append('csrf',HK.csrf);fd.append('channel','ai');fd.append('message',t);if(document.getElementById('useprof').checked)fd.append('use_profile','1');
 document.getElementById('s').disabled=true;
 try{const r=await post(fd);ty.remove();addMsg(box,'bot',r.ok?r.reply:(r.error||'Something went wrong.'),r.note)}catch(e){ty.remove();addMsg(box,'bot','Network error — please try again.')}
 document.getElementById('s').disabled=false}
document.getElementById('f').onsubmit=e=>{e.preventDefault();const i=document.getElementById('m'),v=i.value;i.value='';send(v)};
document.querySelectorAll('#chips button').forEach(b=>b.onclick=()=>send(b.textContent));
document.getElementById('clr').onclick=async()=>{if(!confirm('Clear your AI conversation?'))return;const fd=new FormData();fd.append('csrf',HK.csrf);fd.append('action','clear');fd.append('channel','ai');await post(fd);location.reload()};
</script><?php require 'includes/footer.php';
