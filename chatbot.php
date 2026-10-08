<?php $title='Assistant'; require 'includes/header.php'; require 'includes/ai.php'; require_login();
$msgs=q('SELECT sender,message,created_at FROM chat_messages WHERE user_id=? ORDER BY id DESC LIMIT 60',[$u['id']])->fetchAll();$msgs=array_reverse($msgs);?>
<div class="chatpage"><div class="chat-side">
<div class="card p-3 mb-3"><div class="d-flex align-items-center gap-2 mb-2"><span class="brand-dot text-white"><i class="bi bi-stars"></i></span><b>HealthKey Assistant</b></div>
<span class="badge <?=ai_ready()?'bg-success':'bg-secondary'?> align-self-start"><?=ai_ready()?'AI mode on':'Basic mode (no API key)'?></span>
<p class="small text-muted mt-2 mb-0">Ask anything health-related — conditions, medicines, lab results, first aid, or how HEALTHKEY works.</p></div>
<div class="card p-3 mb-3"><label class="form-check d-flex gap-2 small"><input type="checkbox" id="useprof" class="form-check-input mt-1"><span><b>Use my health profile</b><br><span class="text-muted">Personalises answers (e.g. allergies). It is sent to Google (free tier) only while this is on; leave off if you prefer privacy.</span></span></label></div>
<div class="card p-3"><div class="btn-group w-100"><input type="radio" class="btn-check" name="to" id="tb" value="bot" checked><label class="btn btn-outline-primary btn-sm" for="tb">Ask AI</label><input type="radio" class="btn-check" name="to" id="ta" value="admin"><label class="btn btn-outline-primary btn-sm" for="ta">Contact admin</label></div>
<button class="btn btn-sm btn-link text-danger mt-2" id="clr">Clear conversation</button></div></div>
<div class="card chatbox"><div id="msgs" class="msgs"></div>
<div class="chips-row" id="chips"><?php foreach(['What is hypertension?','Explain how insulin resistance leads to type 2 diabetes','What should I do if someone is having a seizure?','Can I take ibuprofen if I have asthma?','How do I read a cholesterol report?','What is anaphylaxis and how is it treated?'] as $c)echo '<button type="button">'.e($c).'</button>';?></div>
<form id="f" class="inputbar"><input id="m" class="form-control" maxlength="1000" placeholder="Ask a health question…" autocomplete="off" required><button class="btn btn-primary" id="s"><i class="bi bi-send-fill"></i></button></form>
<div class="disc">General information only — not a diagnosis or a substitute for a doctor. In an emergency call 999 / 112.</div></div></div>
<script>
const CSRF=<?=json_encode($_SESSION['csrf']??'')?>,HIST=<?=json_encode($msgs)?>,box=document.getElementById('msgs');
function md(t){t=t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\*\*(.+?)\*\*/g,'<b>$1</b>');let o='',l=false;
 for(const x of t.split('\n')){const m=x.match(/^\s*(?:[-*•]|\d+\.)\s+(.*)/);if(m){if(!l){o+='<ul>';l=true}o+='<li>'+m[1]+'</li>'}else{if(l){o+='</ul>';l=false}if(x.trim())o+='<p>'+x+'</p>'}}return o+(l?'</ul>':'')}
function add(who,txt,note){const d=document.createElement('div');d.className='bub b-'+who;d.innerHTML='<small>'+(who==='bot'?'Assistant':who==='admin'?'Admin':'You')+'</small>'+(who==='user'?'<p>'+txt.replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]))+'</p>':md(txt))+(note?'<em class="note">'+note+'</em>':'');box.appendChild(d);box.scrollTop=box.scrollHeight;return d}
HIST.forEach(h=>add(h.sender,h.message));if(!HIST.length)add('bot','Hi! I can explain medical terms, conditions, medicines, lab results and first aid, or help with HEALTHKEY. What would you like to know?');
async function send(t){if(!t.trim())return;document.getElementById('chips').style.display='none';add('user',t);
 const mode=document.querySelector('[name=to]:checked').value,ty=add('bot','…');ty.classList.add('typing');ty.innerHTML='<small>Assistant</small><span class="dots"><i></i><i></i><i></i></span>';
 const fd=new FormData();fd.append('csrf',CSRF);fd.append('message',t);fd.append('to',mode);if(document.getElementById('useprof').checked)fd.append('use_profile','1');
 document.getElementById('s').disabled=true;
 try{const r=await (await fetch('chat-api.php',{method:'POST',body:fd})).json();ty.remove();
  if(!r.ok)add('bot',r.error||'Something went wrong.');else if(r.reply)add('bot',r.reply,r.note);else add('bot',r.note||'Sent.')}
 catch(e){ty.remove();add('bot','Network error — please try again.')}document.getElementById('s').disabled=false}
document.getElementById('f').onsubmit=e=>{e.preventDefault();const i=document.getElementById('m');const v=i.value;i.value='';send(v)};
document.querySelectorAll('#chips button').forEach(b=>b.onclick=()=>send(b.textContent));
document.getElementById('clr').onclick=async()=>{if(!confirm('Clear your conversation?'))return;const fd=new FormData();fd.append('csrf',CSRF);fd.append('action','clear');await fetch('chat-api.php',{method:'POST',body:fd});location.reload()};
</script><?php require 'includes/footer.php';
