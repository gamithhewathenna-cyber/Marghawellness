<?php
$f1 = setting('f1_name') . ', ' . setting('f1_role');
$f2 = setting('f2_name') . ', ' . setting('f2_role');
?>
<section class="founders" aria-label="Our founders">
    <div class="founder founder--1">
        <div class="founder__text">
            <?= paras(setting('f1_text')) ?>
            <p class="founder__sig"><strong>-<?= e(setting('f1_name')) ?>,</strong> <em><?= e(setting('f1_role')) ?></em></p>
        </div>
        <div class="founder__media founder__media--navy">
            <img src="<?= e(img(setting('f1_image'))) ?>" alt="<?= e($f1) ?>" width="718" height="422" loading="lazy">
        </div>
    </div>
    <div class="founder founder--2">
        <div class="founder__media founder__media--grey">
            <img src="<?= e(img(setting('f2_image'))) ?>" alt="<?= e($f2) ?>" width="719" height="421" loading="lazy">
        </div>
        <div class="founder__text">
            <?= paras(setting('f2_text')) ?>
            <p class="founder__sig founder__sig--sans"><strong>-<?= e(setting('f2_name')) ?>,</strong> <em><?= e(setting('f2_role')) ?></em></p>
        </div>
    </div>
</section>
