<?php $title='Stories'; require 'includes/header.php'; require 'includes/content.php'; $S=stories();?>
<div class="mb-4"><span class="eyebrow">Stories</span><h1 class="fw-bold mt-1" style="font-size:clamp(2rem,5vw,3.4rem)">Real moments this is built for.</h1>
<p class="sub text-muted" style="max-width:40rem">Five illustrative scenarios showing how a HEALTHKEY could help. They are written examples for this prototype, not testimonials from real customers.</p></div>
<div class="row g-4"><?php foreach($S as $slug=>$s):?><div class="col-md-6 col-lg-4"><a class="card s-card" href="<?=BASE_URL?>/story.php?s=<?=e($slug)?>"><div class="art" style="background:#<?=$s['c'][3]?>"><?=person($s['c'][0],$s['c'][1],$s['c'][2],'ffffff',150)?></div>
<div class="bd"><span class="tag mb-2"><?=e($s['cond'])?></span><h5>“<?=e($s['quote'])?>”</h5><p class="text-muted small mb-2"><?=e($s['sum'])?></p><b class="small" style="color:var(--teal-d)">Read story <i class="bi bi-arrow-right"></i></b></div></a></div><?php endforeach;?></div>
<?php require 'includes/footer.php';
