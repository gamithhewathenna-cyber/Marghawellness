<section class="cta-band" aria-labelledby="cta-heading">
    <img class="cta-band__bg" src="<?= e(img(setting('cta_image'))) ?>" alt="" width="1439" height="425" loading="lazy">
    <div class="cta-band__content">
        <p class="eyebrow eyebrow--tan"><?= e(setting('cta_eyebrow')) ?></p>
        <h2 class="cta-band__title" id="cta-heading"><?= rich(setting('cta_title')) ?><span><?= e(setting('cta_subtitle')) ?></span></h2>
        <a class="btn btn--rust" href="<?= e(url('enquire')) ?>"><?= e(setting('cta_button')) ?></a>
    </div>
</section>
