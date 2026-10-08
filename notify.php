<?php require 'includes/bootstrap.php'; header('Content-Type: application/json'); $u=user();
if(!$u)exit(json_encode(['count'=>0,'staff'=>0]));
$c=0;$st=0;$title='';$msg='';$url='';
if($u['role']==='admin'){ // customer messages (admin channel only) + messages from the owner
 $c=(int)q("SELECT COUNT(*) c FROM chat_messages WHERE channel='admin' AND sender='user' AND is_read=0")->fetch()['c'];
 $st=(int)q("SELECT COUNT(*) c FROM staff_messages WHERE admin_id=? AND sender_role='super' AND is_read=0",[$u['id']])->fetch()['c'];
 if($c){$l=q("SELECT u.name,m.message FROM chat_messages m JOIN users u ON u.id=m.user_id WHERE m.channel='admin' AND m.sender='user' AND m.is_read=0 ORDER BY m.id DESC LIMIT 1")->fetch();$title='New message from '.$l['name'];$msg=$l['message'];$url=BASE_URL.'/admin/messages.php';}
 elseif($st){$l=q("SELECT message FROM staff_messages WHERE admin_id=? AND sender_role='super' AND is_read=0 ORDER BY id DESC LIMIT 1",[$u['id']])->fetch();$title='Message from the owner';$msg=$l['message'];$url=BASE_URL.'/admin/staff-chat.php';}
}elseif($u['role']==='superadmin'){ // owner only hears from admins, never customers
 $st=(int)q("SELECT COUNT(*) c FROM staff_messages WHERE sender_role='admin' AND is_read=0")->fetch()['c'];
 if($st){$l=q("SELECT s.message,s.admin_id,u.name FROM staff_messages s JOIN users u ON u.id=s.admin_id WHERE s.sender_role='admin' AND s.is_read=0 ORDER BY s.id DESC LIMIT 1")->fetch();$title='Message from '.$l['name'];$msg=$l['message'];$url=BASE_URL.'/admin/staff-chat.php?aid='.$l['admin_id'];}
}else{
 $c=(int)q("SELECT COUNT(*) c FROM chat_messages WHERE user_id=? AND channel='admin' AND sender='admin' AND is_read=0",[$u['id']])->fetch()['c'];
 if($c){$title='Admin replied';$msg='You have a new reply';$url=BASE_URL.'/support.php';}}
echo json_encode(['count'=>$c,'staff'=>$st,'title'=>$title,'msg'=>mb_substr($msg,0,80),'url'=>$url]);
