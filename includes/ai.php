<?php
// Server-side Google Gemini call (free tier). The API key never reaches the browser.
function ai_ready(){return GEMINI_API_KEY!=='';}
function ai_system($profile=null){
 $s="You are HealthKey Assistant, a knowledgeable, warm health-information assistant inside the HEALTHKEY app (a QR medical-ID bracelet prototype from Malaysia).
Answer ANY health, medicine, anatomy, nutrition, first-aid, lab-result or general knowledge question clearly and in real depth, like a well-read doctor friend: explain mechanisms, give the key facts, mention evidence strength or uncertainty, and say what would change the advice. Handle hard questions directly instead of deflecting. Define terms when asked 'what is X'.
Safety rules: you do not diagnose; for symptoms say what is common vs what is red-flag and when to see a doctor or go to A&E. For emergencies (chest pain, stroke signs, severe bleeding, trouble breathing, unconsciousness, anaphylaxis, overdose) lead with: call 999 (or 112 from a mobile) now. For medication doses, give label-style general ranges only when standard and safe, and tell the user to check the label/pharmacist. If someone expresses self-harm, respond with compassion and urge immediate help (999/112, Befrienders KL 03-7627 2929).
Style: reply in the user's language (English, Bahasa Melayu or a mix). Be concise but complete: short paragraphs, **bold** key terms, bullet lists for steps. No headings. End with a short 'when to see a doctor' line only when relevant. You can also explain how HEALTHKEY works (register, health profile, buy bracelet to reveal QR, claim a tag, scan page shows blood type/allergies/contact, scans are logged).";
 if($profile)$s.="\nThe user chose to share their own profile for personalised answers (private, do not repeat unless relevant): blood type ".($profile['blood_type']?:'n/a').'; allergies: '.($profile['allergies']?:'none listed').'; conditions: '.($profile['medical_conditions']?:'none listed').'; medications: '.($profile['medications']?:'none listed').'.';
 return $s;}
// $msgs = [['role'=>'user|assistant','content'=>...],...]. Returns [text|null, error|null]
function ai_reply(array $msgs,$profile=null){
 $out=[];foreach($msgs as $m){if($out&&end($out)['role']===$m['role'])$out[count($out)-1]['content'].="\n".$m['content'];else $out[]=$m;}
 while($out&&$out[0]['role']!=='user')array_shift($out);
 $contents=array_map(fn($m)=>['role'=>$m['role']==='user'?'user':'model','parts'=>[['text'=>$m['content']]]],$out);
 // BLOCK_ONLY_HIGH so ordinary medical questions (overdose, symptoms) are not wrongly blocked.
 $safety=array_map(fn($c)=>['category'=>$c,'threshold'=>'BLOCK_ONLY_HIGH'],['HARM_CATEGORY_HARASSMENT','HARM_CATEGORY_HATE_SPEECH','HARM_CATEGORY_SEXUALLY_EXPLICIT','HARM_CATEGORY_DANGEROUS_CONTENT']);
 $body=['system_instruction'=>['parts'=>[['text'=>ai_system($profile)]]],'contents'=>$contents,'safetySettings'=>$safety,'generationConfig'=>['maxOutputTokens'=>2500,'temperature'=>0.5]];
 // Try the main model (retrying on temporary 503 overload), then a fallback model.
 $models=array_values(array_unique(array_filter([AI_MODEL,AI_FALLBACK_MODEL])));$last='unknown';
 foreach($models as $mi=>$model){
  for($try=0;$try<($mi===0?3:1);$try++){
   if($try)sleep($try*2);
   [$t,$e,$code]=ai_call($model,$body);
   if($t)return [$t,null];
   $last="[$model] $e";
   if($code!==503&&$code!==0)break;   // only retry temporary overload / network blips
  }}
 return [null,$last];}
// One HTTP call. Returns [text|null, error|null, httpCode]
function ai_call($model,$body){
 $ch=curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent');
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_TIMEOUT=>40,
  CURLOPT_HTTPHEADER=>['content-type: application/json','x-goog-api-key: '.GEMINI_API_KEY],CURLOPT_POSTFIELDS=>json_encode($body)]);
 $r=curl_exec($ch);$err=curl_error($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 if($r===false)return [null,'network: '.$err,0];
 $j=json_decode($r,true);
 if($code!==200)return [null,'api '.$code.': '.($j['error']['message']??'unknown'),$code];
 $t='';foreach($j['candidates'][0]['content']['parts']??[] as $p)if(isset($p['text']))$t.=$p['text'];
 $t=trim($t);
 return [$t?:null,$t?null:'empty/blocked: '.($j['candidates'][0]['finishReason']??$j['promptFeedback']['blockReason']??'?'),$code];}
