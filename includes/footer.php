</main>
<footer class="foot"><div class="container"><div class="row g-4"><div class="col-lg-4"><div class="navbar-brand text-white mb-2 fs-4 d-flex align-items-center gap-2"><span class="brand-dot"><i class="bi bi-qr-code-scan"></i></span>HEALTHKEY</div><p class="small">A QR health bracelet prototype. Your info, one scan away when it matters most.</p></div>
<div class="col-6 col-lg-2"><h6>Product</h6><a href="<?=BASE_URL?>/#bracelets">Bracelets</a><a href="<?=BASE_URL?>/#how">How it works</a><a href="<?=BASE_URL?>/claim-qr.php">Claim a tag</a></div>
<div class="col-6 col-lg-2"><h6>Learn</h6><a href="<?=BASE_URL?>/stories.php">Stories</a><a href="<?=BASE_URL?>/#responders">For responders</a><a href="<?=BASE_URL?>/#faq">FAQ</a></div>
<div class="col-lg-4"><h6>Important</h6><p class="small mb-0">HEALTHKEY is an academic prototype, not a medical device. In an emergency, always call your local emergency number (999 in Malaysia).</p></div></div>
<hr style="border-color:rgba(255,255,255,.12)"><div class="small d-flex justify-content-between flex-wrap gap-2"><span>&copy; <?=date('Y')?> HEALTHKEY</span><span>Built with PHP &amp; MySQL</span></div></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><?php if(user()):?><script>
(function(){const base=<?=json_encode(BASE_URL)?>;let prev=+(sessionStorage.getItem('hkc')||0);
function toast(r){const d=document.createElement('div');d.className='hk-toast';d.innerHTML='<b>'+(r.role==='admin'?'New message from '+r.name:'Admin replied')+'</b><div class="small" style="color:#9fb3c8">'+r.msg.replace(/[<&]/g,'')+'</div><a href="'+r.url+'">Open chat →</a>';document.body.appendChild(d);setTimeout(()=>d.remove(),9000)}
async function tick(){try{const r=await (await fetch(base+'/notify.php')).json();document.querySelectorAll('.hk-badge').forEach(b=>{if(b.closest('.thread'))return;b.textContent=r.count;b.style.display=r.count?'inline-block':'none'});
 if(r.count>prev&&!location.pathname.match(/messages\.php|support\.php/))toast(r);prev=r.count;sessionStorage.setItem('hkc',prev)}catch(e){}}
tick();setInterval(tick,10000)})();</script><?php endif;?></body></html>
