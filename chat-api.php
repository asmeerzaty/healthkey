<?php require 'includes/bootstrap.php'; require 'includes/ai.php'; require_login();
$u=user(); header('Content-Type: application/json'); $uid=$u['id'];
function out($a){echo json_encode($a);exit;}
$ch=(($_REQUEST['channel']??'ai')==='admin')?'admin':'ai';   // two SEPARATE conversations
$act=$_REQUEST['action']??'';
// User polls for new admin replies (admin channel only), then marks them read.
if($act==='poll'){$after=(int)($_GET['after']??0);
 $rows=q("SELECT id,sender,message FROM chat_messages WHERE user_id=? AND channel='admin' AND sender='admin' AND id>? ORDER BY id",[$uid,$after])->fetchAll();
 if($rows)q("UPDATE chat_messages SET is_read=1,read_at=NOW() WHERE user_id=? AND channel='admin' AND sender='admin' AND is_read=0",[$uid]);
 $seen=array_map(fn($x)=>['id'=>(int)$x['id'],'at'=>$x['read_at']?seen_label($x['read_at']):''],q("SELECT id,read_at FROM chat_messages WHERE user_id=? AND channel='admin' AND sender='user' AND is_read=1",[$uid])->fetchAll());
 out(['ok'=>true,'messages'=>$rows,'seen'=>$seen]);}
if($act==='clear'){if($ch==='ai')q("DELETE FROM chat_messages WHERE user_id=? AND channel='ai'",[$uid]);out(['ok'=>true]);}
$m=trim($_POST['message']??'');if($m===''||mb_strlen($m)>1000)out(['ok'=>false,'error'=>'Message must be 1–1000 characters.']);
// ---- Contact-admin channel: no AI involved; flagged unread so the admin gets a notification ----
if($ch==='admin'){q("INSERT INTO chat_messages(user_id,sender,channel,message,is_read) VALUES(?, 'user','admin',?,0)",[$uid,$m]);out(['ok'=>true,'id'=>(int)db()->lastInsertId()]);}
// ---- AI channel ----
q("INSERT INTO chat_messages(user_id,sender,channel,message) VALUES(?, 'user','ai',?)",[$uid,$m]);
if(preg_match('/suicid|kill myself|end my life|self[- ]?harm|want to die|bunuh diri/i',$m)){
 $r="I'm really sorry you're going through this — you matter, and you don't have to carry it alone. If you might act on these thoughts, please call **999** (or **112** from a mobile) now, or go to the nearest A&E. You can also talk to someone right away at **Befrienders KL: 03-7627 2929** (24 hours). If you can, tell someone near you how you feel. I'm here to keep talking with you too.";
 q("INSERT INTO chat_messages(user_id,sender,channel,message) VALUES(?, 'bot','ai',?)",[$uid,$r]);out(['ok'=>true,'reply'=>$r]);}
$note=null;$e=null;
if(ai_ready()){
 $n=q("SELECT COUNT(*) c FROM chat_messages WHERE user_id=? AND channel='ai' AND sender='user' AND created_at>NOW()-INTERVAL 1 HOUR",[$uid])->fetch()['c'];
 if($n>AI_HOURLY_LIMIT){$r='You have reached the hourly question limit. Please try again a bit later.';}
 else{$rows=q("SELECT sender,message FROM chat_messages WHERE user_id=? AND channel='ai' AND sender IN('user','bot') ORDER BY id DESC LIMIT 14",[$uid])->fetchAll();$h=[];
  foreach(array_reverse($rows) as $x)$h[]=['role'=>$x['sender']==='user'?'user':'assistant','content'=>$x['message']];
  $prof=!empty($_POST['use_profile'])?profile_of($uid):null;
  [$r,$e]=ai_reply($h,$prof?:null);
  if(!$r){error_log('HealthKey AI: '.$e);$r=bot_reply($m);$note=(strpos((string)$e,'api 429')!==false)?'The free AI limit was reached for now, so this is a basic answer. Try again in a minute.':'AI is unavailable right now, so this is a basic answer.';}}
}else{$r=bot_reply($m);$note='AI mode is off (no API key set), so this is the basic rule-based bot.';}
if(AI_DEBUG&&!empty($e))$note.=' [debug: '.$e.']';
q("INSERT INTO chat_messages(user_id,sender,channel,message) VALUES(?, 'bot','ai',?)",[$uid,$r]);
out(['ok'=>true,'reply'=>$r,'note'=>$note]);
