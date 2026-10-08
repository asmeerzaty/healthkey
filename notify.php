<?php require 'includes/bootstrap.php'; header('Content-Type: application/json'); $u=user();
if(!$u)exit(json_encode(['count'=>0]));
if($u['role']==='admin'){ // unread messages sent to ADMIN only (never AI chats)
 $r=q("SELECT COUNT(*) c,COUNT(DISTINCT user_id) n FROM chat_messages WHERE channel='admin' AND sender='user' AND is_read=0")->fetch();
 $l=q("SELECT u.name,m.message FROM chat_messages m JOIN users u ON u.id=m.user_id WHERE m.channel='admin' AND m.sender='user' AND m.is_read=0 ORDER BY m.id DESC LIMIT 1")->fetch();
 echo json_encode(['role'=>'admin','count'=>(int)$r['c'],'people'=>(int)$r['n'],'name'=>$l['name']??'','msg'=>$l?mb_substr($l['message'],0,80):'','url'=>BASE_URL.'/admin/messages.php']);
}else{
 $c=q("SELECT COUNT(*) c FROM chat_messages WHERE user_id=? AND channel='admin' AND sender='admin' AND is_read=0",[$u['id']])->fetch()['c'];
 echo json_encode(['role'=>'user','count'=>(int)$c,'name'=>'Admin','msg'=>'You have a new reply','url'=>BASE_URL.'/support.php']);}
