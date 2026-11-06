<section class="destination" aria-labelledby="destination-heading">
    <div class="destination__text">
        <p class="eyebrow eyebrow--tan"><?= e(setting('ds_eyebrow')) ?></p>
        <h2 class="destination__title" id="destination-heading"><?= rich(setting('ds_title')) ?></h2>
        <div class="destination__copy">
            <?= paras(setting('ds_copy')) ?>
        </div>
        <a class="btn btn--rust btn--arrow" href="<?= e(url('sri-lanka')) ?>"><?= e(setting('ds_button')) ?> <?= arrow_icon() ?></a>
    </div>
    <div class="destination__media">
        <img src="<?= e(img(setting('ds_image'))) ?>" alt="<?= e(setting('ds_image_alt')) ?>" width="782" height="796" loading="lazy">
    </div>
</section>
