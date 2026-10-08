<?php require 'includes/bootstrap.php'; require 'includes/ai.php'; require_login();
$u=user(); header('Content-Type: application/json'); $uid=$u['id'];
function out($a){echo json_encode($a);exit;}
if(($_POST['action']??'')==='clear'){q('DELETE FROM chat_messages WHERE user_id=? AND sender<>\'admin\'',[$uid]);out(['ok'=>true]);}
$m=trim($_POST['message']??'');if($m===''||mb_strlen($m)>1000)out(['ok'=>false,'error'=>'Message must be 1–1000 characters.']);
q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'user',?)",[$uid,$m]);
if(($_POST['to']??'bot')==='admin')out(['ok'=>true,'reply'=>null,'note'=>'Sent to admin. Replies will appear here.']);
// Crisis pre-check: always answer with real help first.
if(preg_match('/suicid|kill myself|end my life|self[- ]?harm|want to die|bunuh diri/i',$m)){
 $r="I'm really sorry you're going through this — you matter, and you don't have to carry it alone. If you might act on these thoughts, please call **999** (or **112** from a mobile) now, or go to the nearest A&E. You can also talk to someone right away at **Befrienders KL: 03-7627 2929** (24 hours). If you can, tell someone near you how you feel. I'm here to keep talking with you too.";
 q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'bot',?)",[$uid,$r]);out(['ok'=>true,'reply'=>$r]);}
$note=null;
if(ai_ready()){
 $n=q("SELECT COUNT(*) c FROM chat_messages WHERE user_id=? AND sender='user' AND created_at>NOW()-INTERVAL 1 HOUR",[$uid])->fetch()['c'];
 if($n>AI_HOURLY_LIMIT){$r='You have reached the hourly question limit. Please try again a bit later.';}
 else{$rows=q("SELECT sender,message FROM chat_messages WHERE user_id=? AND sender IN('user','bot') ORDER BY id DESC LIMIT 14",[$uid])->fetchAll();$h=[];
  foreach(array_reverse($rows) as $x)$h[]=['role'=>$x['sender']==='user'?'user':'assistant','content'=>$x['message']];
  $prof=!empty($_POST['use_profile'])?profile_of($uid):null;
  [$r,$e]=ai_reply($h,$prof?:null);
  if(!$r){error_log('HealthKey AI: '.$e);$r=bot_reply($m);$note=(strpos((string)$e,'api 429')===0)?'The free AI limit was reached for now, so this is a basic answer. Try again in a minute.':'AI is unavailable right now, so this is a basic answer.';}}
}else{$r=bot_reply($m);$note='AI mode is off (no API key set), so this is the basic rule-based bot.';}
q("INSERT INTO chat_messages(user_id,sender,message) VALUES(?, 'bot',?)",[$uid,$r]);
out(['ok'=>true,'reply'=>$r,'note'=>$note]);
