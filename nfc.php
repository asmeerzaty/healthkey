<?php require 'includes/bootstrap.php'; require_login(); $u=user();
if($u['role']!=='user')redirect('admin/');
$p=profile_of($u['id']);
if(!$p||!profile_complete($u['id'])){flash('Complete your health profile first, then we can build your NFC text.','warning');redirect('health-profile.php');}
$qr=qr_of($u['id']);$link=($qr&&$qr['status']==='active')?scan_url($qr['unique_token']):null;
$saved=$p['nfc_opts']?json_decode($p['nfc_opts'],true):null;
// Options: live form (GET) > what you last wrote > sensible defaults
$o=nfc_opts_parse(isset($_GET['go'])?$_GET:($saved?:nfc_opts_default()));if(!$link)$o['link']=0;
if($_SERVER['REQUEST_METHOD']==='POST'){$act=$_POST['act']??'';
 if($act==='mark'){$po=nfc_opts_parse($_POST);if(!$link)$po['link']=0;$b=nfc_build($u,$p,$po,$link);
  if(!$b['fits']){flash('That text is too big for this tag. Pick a bigger tag or switch off an option.','danger');redirect('nfc.php');}
  q('UPDATE patient_profiles SET nfc_opts=?,nfc_hash=?,nfc_written_at=NOW() WHERE user_id=?',[json_encode($po),$b['hash'],$u['id']]);flash('Saved. We will tell you if your profile changes and the tag needs rewriting.');redirect('nfc.php');}
 if($act==='forget'){q('UPDATE patient_profiles SET nfc_opts=NULL,nfc_hash=NULL,nfc_written_at=NULL WHERE user_id=?',[$u['id']]);flash('Okay, we stopped tracking your tag.');redirect('nfc.php');}
 redirect('nfc.php');}
$cur=nfc_build($u,$p,$o,$link);
$stale=null;if($p['nfc_written_at']){$so=nfc_opts_parse($saved?:[]);if(!$link)$so['link']=0;$stale=nfc_build($u,$p,$so,$link)['hash']!==$p['nfc_hash'];}
$pct=min(100,round($cur['used']/$cur['cap']*100));$mcls=!$cur['fits']?'over':($pct>85?'hot':'');
$names=['cond'=>'Conditions','meds'=>'Medication','dob'=>'Date of birth','doc'=>'Doctor'];
$title='NFC tag text';require 'includes/header.php';?>
<div class="page-head"><div><h2 class="mb-1">Your NFC tag text</h2><span class="text-muted">We write the short version. You copy it, paste it into a free NFC app, and write it to your bracelet once.</span></div><a class="btn btn-outline-primary" href="my-qr.php"><i class="bi bi-qr-code"></i> My QR</a></div>

<?php if($stale===null):?><div class="nfc-status none mb-4"><i class="bi bi-broadcast"></i><div>No tag written yet.<div class="small fw-normal text-muted">Follow the steps below, then press “I've written it” so we can watch for changes.</div></div></div>
<?php elseif($stale):?><div class="nfc-status stale mb-4"><i class="bi bi-exclamation-triangle-fill"></i><div>Your tag is out of date.<div class="small fw-normal">Your profile or QR link changed after you wrote it on <?=e(date('j M Y',strtotime($p['nfc_written_at'])))?>. Copy the new text below and write it again.</div></div></div>
<?php else:?><div class="nfc-status ok mb-4"><i class="bi bi-patch-check-fill"></i><div>Your tag matches your profile.<div class="small fw-normal">Last written <?=e(date('j M Y, g:i A',strtotime($p['nfc_written_at'])))?>.</div></div></div><?php endif;?>

<div class="row g-4"><div class="col-lg-5">
<form method="get" class="card p-4" id="optf"><input type="hidden" name="go" value="1"><h5 class="fw-bold mb-3">What goes on the tag</h5>
<div class="form-label">Your tag chip</div><div class="seg mb-3"><?php foreach(['NTAG213'=>'213 · tiny','NTAG215'=>'215 · standard','NTAG216'=>'216 · roomy'] as $k=>$l):?><input type="radio" name="tag" id="t<?=$k?>" value="<?=$k?>" <?=$o['tag']===$k?'checked':''?>><label for="t<?=$k?>"><?=$l?></label><?php endforeach;?></div>
<div class="p-3 rounded-3 mb-2" style="background:var(--teal-l)"><b class="small">Always included</b><div class="small">Name, blood type, allergies and your emergency contact.</div></div>
<?php foreach([['cond','Medical conditions','e.g. asthma, diabetes'],['meds','Current medication','What responders must not interrupt or double up'],['dob','Date of birth','Helps hospitals find your records'],['doc','Doctor name','Who treats you regularly']] as [$k,$l,$d]):?>
<label class="opt"><input class="form-check-input" type="checkbox" name="<?=$k?>" value="1" <?=$o[$k]?'checked':''?>><span><b><?=$l?></b><?=$d?></span></label><?php endforeach;?>
<label class="opt"><input class="form-check-input" type="checkbox" name="link" value="1" <?=$o['link']?'checked':''?> <?=$link?'':'disabled'?>><span><b>Also add my live link</b><?=$link?'Opens your always-up-to-date emergency page when there is signal.':'Available once your QR is active.'?></span></label>
<noscript><button class="btn btn-primary mt-3">Update preview</button></noscript></form>
<div class="card p-4 mt-3"><div class="d-flex justify-content-between mb-2"><b>Space used</b><span class="small text-muted"><?=$cur['used']?> of <?=$cur['cap']?> bytes</span></div><div class="meter <?=$mcls?>"><i style="width:<?=$pct?>%"></i></div>
<?php if(!$cur['fits']):?><p class="small text-danger mt-2 mb-0">Too big for this chip, even after trimming. Choose NTAG215 or NTAG216.</p><?php elseif($cur['dropped']||$cur['shrunk']):?><p class="small mt-2 mb-0" style="color:#7a4a05"><?php if($cur['dropped'])echo 'No room for: '.e(implode(', ',array_map(fn($k)=>$names[$k],$cur['dropped']))).'. ';if($cur['shrunk'])echo 'Long entries were shortened.';?></p><?php else:?><p class="small text-muted mt-2 mb-0">Everything you picked fits.</p><?php endif;?></div>
</div>

<div class="col-lg-7"><div class="card p-4"><h5 class="fw-bold">1. Copy the emergency text</h5><p class="small text-muted">This is what a phone shows when someone taps your tag. It needs no signal.</p>
<div class="copybox"><textarea id="txt" class="form-control" rows="<?=substr_count($cur['text'],"\n")+2?>" readonly><?=e($cur['text'])?></textarea><button type="button" class="btn btn-teal btn-sm" onclick="cp('txt',this)"><i class="bi bi-clipboard"></i> Copy</button></div>
<div class="chipline"><span>Name</span><span>Blood type</span><span>Allergies</span><span>Emergency contact</span><?php foreach(['cond','meds','dob','doc'] as $k)if($o[$k]):?><span class="<?=in_array($k,$cur['dropped'])?'off':''?>"><?=$names[$k]?></span><?php endif;?></div>
<?php if($cur['link']):?><h5 class="fw-bold mt-4">2. Copy your live link <span class="small fw-normal text-muted">(optional)</span></h5><div class="copybox"><input id="lnk" class="form-control" readonly value="<?=e($cur['link'])?>"><button type="button" class="btn btn-teal btn-sm" onclick="cp('lnk',this)"><i class="bi bi-clipboard"></i> Copy</button></div><?php endif;?></div>

<div class="card p-4 mt-3"><h5 class="fw-bold mb-3">Write it to your tag</h5><ol class="steps2 mb-0">
<li><b>Get a tag.</b> <span class="text-muted">An NTAG215 sticker or coin. Check the listing says NTAG215 (or 216), not 213 or MIFARE.</span></li>
<li><b>Install a free NFC app.</b> <span class="text-muted">NFC Tools works on Android and iPhone.</span></li>
<li><b>Add the text.</b> <span class="text-muted">In the app choose Write, then Add a record, then Text, and paste what you copied.</span></li>
<?php if($cur['link']):?><li><b>Add the link.</b> <span class="text-muted">Add another record, choose URL, and paste the link. Keep the text record first.</span></li><?php endif;?>
<li><b>Write and check.</b> <span class="text-muted">Tap Write and hold your phone to the tag. Then open Read and tap the tag again to confirm it shows your text.</span></li>
<li><b>Lock it.</b> <span class="text-muted">Look under Other in NFC Tools for a password option, so strangers cannot overwrite the tag.</span></li></ol>
<hr><div class="d-flex gap-2 flex-wrap align-items-center"><form method="post" id="markform" class="d-inline"><?=csrf()?><input type="hidden" name="act" value="mark"><?php foreach($o as $k=>$v)echo '<input type="hidden" name="'.e($k).'" value="'.e($v).'">';?><button class="btn btn-primary" <?=$cur['fits']?'':'disabled'?>><i class="bi bi-check2-circle"></i> I've written it to my tag</button></form>
<button type="button" class="btn btn-outline-primary d-none" id="webnfc" <?=$cur['fits']?'':'disabled'?>><i class="bi bi-broadcast"></i> Write from this phone</button>
<?php if($stale!==null):?><form method="post" class="d-inline" onsubmit="return confirm('Stop tracking this tag?')"><?=csrf()?><input type="hidden" name="act" value="forget"><button class="btn btn-link text-muted">Stop tracking</button></form><?php endif;?></div>
<div id="wn" class="small text-muted mt-2"></div></div>

<div class="card p-4 mt-3" style="border-color:#f0c47a;background:#fffaf0"><h6 class="fw-bold"><i class="bi bi-shield-exclamation"></i> Before you write</h6><p class="small mb-0">Anyone who taps the tag can read this text, and unlike the QR it cannot be switched off from here. That is why only the essentials go on it. If you lose the bracelet, rewrite or erase the tag yourself, or protect it with a password from the start. Your full profile is never written to the tag.</p></div></div></div>
<script>
async function cp(id,btn){const el=document.getElementById(id);try{await navigator.clipboard.writeText(el.value)}catch(e){el.select();document.execCommand('copy')}const h=btn.innerHTML;btn.innerHTML='<i class="bi bi-check2"></i> Copied';setTimeout(()=>btn.innerHTML=h,1800)}
document.querySelectorAll('#optf input').forEach(i=>i.addEventListener('change',()=>document.getElementById('optf').submit()));
const TXT=<?=json_encode($cur['text'])?>,LNK=<?=json_encode($cur['link'])?>;
if('NDEFReader' in window&&window.isSecureContext){const b=document.getElementById('webnfc');b.classList.remove('d-none');b.onclick=async()=>{const m=document.getElementById('wn');m.textContent='Hold your phone against the tag…';
 try{const recs=[{recordType:'text',data:TXT,lang:'en'}];if(LNK)recs.push({recordType:'url',data:LNK});await new NDEFReader().write({records:recs});document.getElementById('markform').submit()}catch(e){m.textContent='Could not write: '+(e.message||e)}}}
</script>
<?php require 'includes/footer.php';
