function md(t){t=t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\*\*(.+?)\*\*/g,'<b>$1</b>');let o='',l=false;
 for(const x of t.split('\n')){const m=x.match(/^\s*(?:[-*•]|\d+\.)\s+(.*)/);if(m){if(!l){o+='<ul>';l=true}o+='<li>'+m[1]+'</li>'}else{if(l){o+='</ul>';l=false}if(x.trim())o+='<p>'+x+'</p>'}}return o+(l?'</ul>':'')}
const LABEL={bot:'Assistant',admin:'Admin',user:'You'};
function addMsg(box,who,txt,note){const d=document.createElement('div');d.className='bub b-'+who;
 d.innerHTML='<small>'+LABEL[who]+'</small>'+(who==='user'?'<p>'+txt.replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]))+'</p>':md(txt))+(note?'<em class="note">'+note+'</em>':'');
 box.appendChild(d);box.scrollTop=box.scrollHeight;return d}
async function post(fd){return (await fetch(HK.base+'/chat-api.php',{method:'POST',body:fd})).json()}
