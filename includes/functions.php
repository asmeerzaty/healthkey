<?php
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function redirect($p){header('Location: '.BASE_URL.'/'.$p);exit;}
function flash($m=null,$t='success'){if($m){$_SESSION['f']=[$m,$t];return '';}if(!empty($_SESSION['f'])){[$m,$t]=$_SESSION['f'];unset($_SESSION['f']);return "<div class='alert alert-$t'>".e($m)."</div>";}return '';}
function new_token(){return bin2hex(random_bytes(32));} // QR contains ONLY this random token, never medical data
function scan_url($token){$s=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';return "$s://{$_SERVER['HTTP_HOST']}".BASE_URL."/scan.php?token=$token";}
function profile_of($uid){return q('SELECT * FROM patient_profiles WHERE user_id=?',[$uid])->fetch();}
function profile_complete($uid){$p=profile_of($uid);return $p&&$p['blood_type']&&$p['emergency_contact_name']&&$p['emergency_contact_phone'];}
function qr_of($uid){return q('SELECT * FROM qr_codes WHERE user_id=?',[$uid])->fetch();}
function has_paid($uid){return (bool)q("SELECT 1 FROM orders WHERE user_id=? AND payment_status='paid' AND qr_revealed=1",[$uid])->fetch();}
// Claimed QR becomes active once the health profile is complete (admin-disabled QRs stay disabled).
function sync_qr($uid){if(profile_complete($uid))q("UPDATE qr_codes SET status='active' WHERE user_id=? AND status='claimed'",[$uid]);}
// Give a user a QR: take unclaimed stock tag, or mint a new one. Ownership is enforced server-side via user_id.
function assign_qr($uid){if(qr_of($uid))return;$s=q("SELECT id FROM qr_codes WHERE user_id IS NULL AND status='unclaimed' ORDER BY id LIMIT 1")->fetch();
 if($s)q("UPDATE qr_codes SET user_id=?,status='claimed' WHERE id=?",[$uid,$s['id']]);else q("INSERT INTO qr_codes(user_id,unique_token,status) VALUES(?,?, 'claimed')",[$uid,new_token()]);sync_qr($uid);}
// Rule-based chatbot. Replace body with an API call later (same signature).
function bot_reply($t){$t=strtolower($t);$r=[
 ['unconscious|faint|collapse','Check whether the person responds and is breathing normally. Call emergency services (999 in Malaysia) immediately and follow the responder\'s instructions.'],
 ['blood type','Blood type describes the antigens on red blood cells (A, B, AB or O) plus Rh status (+/-).'],
 ['qr|scan','Anyone can scan your bracelet QR without logging in. It opens your emergency page showing only the info you chose to share.'],
 ['allerg','Listing allergies on your profile helps responders avoid giving you a dangerous medication.'],
 ['bleed','Apply firm direct pressure with a clean cloth and call emergency services.'],
 ['choking','If the person cannot breathe or speak, call emergency services and give back blows / abdominal thrusts if trained.'],
 ['hello|hi|hey','Hello! Ask me a basic first-aid or HEALTHKEY question, or choose "Contact Admin".'],
 ['buy|purchase|price','Go to the Bracelet page, click Buy Now and complete the demo checkout. Your QR is revealed after payment.']];
 foreach($r as [$k,$a])if(preg_match("/$k/",$t))return $a;
 return 'Sorry, I only know basic topics (first aid, blood type, QR, allergies). Try "Contact Admin" for other questions.';}
