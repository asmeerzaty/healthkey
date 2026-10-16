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

// 'Seen' time label: '10:42 PM' today, otherwise '7 Oct, 10:42 PM'
function seen_label($dt){$t=strtotime($dt);return date('Y-m-d',$t)===date('Y-m-d')?date('g:i A',$t):date('j M, g:i A',$t);}

// ================= v2 helpers =================
// ---- Owner-uploaded photos (bracelets + stories) ----
function img_dir($kind){return __DIR__.'/../assets/img/'.$kind;}
function img_url($kind,$file){$p=img_dir($kind).'/'.$file;return ($file&&is_file($p))?BASE_URL.'/assets/img/'.$kind.'/'.rawurlencode($file).'?v='.filemtime($p):null;}
// Validates and stores an uploaded JPG/PNG/WebP. Returns [filename|null, error|null]. The extension comes from the detected MIME type, never from the user's filename.
function save_upload($field,$kind,$basename){
 if(empty($_FILES[$field])||$_FILES[$field]['error']===UPLOAD_ERR_NO_FILE)return [null,null];
 $f=$_FILES[$field];
 if($f['error']!==UPLOAD_ERR_OK)return [null,'Upload failed (code '.$f['error'].'). Try a smaller image.'];
 if($f['size']>5*1024*1024)return [null,'That image is larger than 5 MB.'];
 $info=@getimagesize($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$info['mime']??'']??null;
 if(!$ext)return [null,'Please upload a JPG, PNG or WebP image.'];
 $dir=img_dir($kind);if(!is_dir($dir))@mkdir($dir,0755,true);
 $name=$basename.'.'.$ext;
 if(!@move_uploaded_file($f['tmp_name'],$dir.'/'.$name))return [null,'Could not save the image (is assets/img/'.$kind.' writable?).'];
 return [$name,null];}
function story_img($slug){foreach(['jpg','jpeg','png','webp'] as $x){if($u=img_url('stories',$slug.'.'.$x))return $u;}return null;}
function ua_label($ua){$ua=(string)$ua;foreach(['iPhone'=>'iPhone','iPad'=>'iPad','Android'=>'Android phone','Windows'=>'Windows PC','Macintosh'=>'Mac','Linux'=>'Linux'] as $k=>$v)if(stripos($ua,$k)!==false)return $v;return $ua?'Unknown device':'—';}

// ---- NFC tag text (the short, important-only copy the user pastes into an NFC app) ----
const NFC_TAGS=['NTAG213'=>137,'NTAG215'=>492,'NTAG216'=>868]; // max NDEF message bytes per chip
function nfc_clean($s,$max){$s=preg_replace('/[\x00-\x1F\x7F]+/u',' ',(string)$s)??'';$s=trim(preg_replace('/\s+/u',' ',$s)??'');return mb_strcut($s,0,$max,'UTF-8');}
function nfc_opts_default(){return ['tag'=>'NTAG215','cond'=>1,'meds'=>1,'dob'=>0,'doc'=>0,'link'=>1];}
function nfc_opts_parse($src){$o=['tag'=>isset(NFC_TAGS[$src['tag']??''])?$src['tag']:'NTAG215'];foreach(['cond','meds','dob','doc','link'] as $k)$o[$k]=!empty($src[$k])?1:0;return $o;}
// Always keeps: name, blood type, allergies, emergency contact. Optional lines are dropped (doctor, DOB, meds, conditions) only if the tag runs out of room.
function nfc_build($u,$p,$o,$link=null){
 $cap=NFC_TAGS[$o['tag']]??492;$useLink=($o['link']&&$link);
 $linkBytes=$useLink?5+strlen(preg_replace('#^https?://#','',$link)):0;
 $budget=$cap-10-8-$linkBytes; // text-record overhead + safety margin + optional link record
 $v=['name'=>nfc_clean($u['name'],40),'blood'=>nfc_clean($p['blood_type'],5),'allergy'=>nfc_clean($p['allergies'],90),'cond'=>nfc_clean($p['medical_conditions'],90),'meds'=>nfc_clean($p['medications'],90),
  'dob'=>$p['date_of_birth']?:'','doc'=>nfc_clean($p['doctor_name'],30),'cn'=>nfc_clean($p['emergency_contact_name'],30),'cr'=>nfc_clean($p['emergency_contact_relationship'],20),'cp'=>preg_replace('/[^0-9+\-() ]/','',(string)$p['emergency_contact_phone'])];
 $want=['cond'=>(bool)$o['cond'],'meds'=>(bool)$o['meds'],'dob'=>(bool)$o['dob'],'doc'=>(bool)$o['doc']];$inc=$want;
 $lines=function()use(&$v,&$inc){$L=['HEALTHKEY EMERGENCY',$v['name'],'BLOOD: '.$v['blood']];
  if($v['allergy'])$L[]='ALLERGY: '.$v['allergy'];if($inc['cond']&&$v['cond'])$L[]='COND: '.$v['cond'];if($inc['meds']&&$v['meds'])$L[]='MEDS: '.$v['meds'];
  $L[]='CALL: '.trim($v['cn'].($v['cr']?' ('.$v['cr'].')':'').' '.$v['cp']);if($inc['dob']&&$v['dob'])$L[]='DOB: '.$v['dob'];if($inc['doc']&&$v['doc'])$L[]='DR: '.$v['doc'];return implode("\n",$L);};
 $stamp="\nUPD: ".date('Y-m-d');$shrunk=false;
 foreach(['doc','dob','meds','cond'] as $k){if(strlen($lines().$stamp)<=$budget)break;$inc[$k]=false;}
 for($i=0;$i<80&&strlen($lines().$stamp)>$budget;$i++){$best=null;foreach(['allergy','cond','meds','name'] as $k){if(($k==='cond'&&!$inc['cond'])||($k==='meds'&&!$inc['meds']))continue;if(strlen($v[$k])>14&&($best===null||strlen($v[$k])>strlen($v[$best])))$best=$k;}
  if($best===null)break;$v[$best]=rtrim(mb_strcut($v[$best],0,strlen($v[$best])-14,'UTF-8')).'...';$shrunk=true;}
 $body=$lines();$text=$body.$stamp;$used=strlen($text)+10+$linkBytes;
 $dropped=[];foreach($want as $k=>$w)if($w&&!$inc[$k]&&$v[$k]!=='')$dropped[]=$k;
 return ['text'=>$text,'link'=>$useLink?$link:null,'cap'=>$cap,'used'=>$used,'fits'=>$used<=$cap-8,'dropped'=>$dropped,'shrunk'=>$shrunk,'hash'=>md5($body.'|'.($useLink?$link:'').'|'.$o['tag'])];}
