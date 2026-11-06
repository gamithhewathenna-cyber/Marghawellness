<?php /** @var array $retreats */ ?>
<section class="wellness" aria-labelledby="wellness-heading">
    <div class="wellness__inner">
        <div class="wellness__intro">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wl_eyebrow')) ?></p>
            <h2 class="wellness__title" id="wellness-heading"><?= rich(setting('wl_title')) ?></h2>
            <div class="wellness__copy">
                <?= paras(setting('wl_copy')) ?>
            </div>
            <a class="btn btn--rust btn--arrow" href="<?= e(url('wellness-holidays')) ?>"><?= e(setting('wl_button')) ?> <?= arrow_icon() ?></a>
        </div>

        <div class="retreat-track" data-drag-scroll tabindex="0" aria-label="Wellness retreats">
            <?php foreach ($retreats as $r): ?>
            <article class="retreat-card">
                <a href="<?= e(url('retreats')) ?>#<?= e($r['slug']) ?>" class="retreat-card__media" draggable="false">
                    <img src="<?= e(img($r['image'])) ?>" alt="<?= e($r['title']) ?>" width="297" height="447" loading="lazy" draggable="false">
                </a>
                <span class="retreat-card__tag"><?= e($r['duration']) ?></span>
                <h3 class="retreat-card__title"><a href="<?= e(url('retreats')) ?>#<?= e($r['slug']) ?>" draggable="false"><?= e($r['title']) ?></a></h3>
                <p class="retreat-card__text"><?= e($r['summary']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
