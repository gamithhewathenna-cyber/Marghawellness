<?php /** @var array $packages  Wellness Packages (Admin › Wellness Packages) */ ?>
<section class="wellness" aria-labelledby="wellness-heading">
    <div class="wellness__inner">
        <div class="wellness__intro">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wl_eyebrow')) ?></p>
            <h2 class="wellness__title" id="wellness-heading"><?= rich(setting('wl_title')) ?></h2>
            <div class="wellness__copy">
                <?= paras(setting('wl_copy')) ?>
            </div>
            <a class="btn btn--rust btn--arrow" href="<?= e(page_url('wellness')) ?>"><?= e(setting('wl_button')) ?> <?= arrow_icon() ?></a>
        </div>

        <div class="retreat-track" data-drag-scroll tabindex="0" aria-label="Wellness packages">
            <?php foreach ($packages as $r):
                $link   = package_url((string) $r['slug']);
                $nights = trim((string) $r['nights']);
                $tag    = implode(' · ', array_filter([wellness_category_name('depth', (string) $r['depth']), $nights !== '' ? str_replace('-', '–', $nights) . ' Nights' : ''])); ?>
            <article class="retreat-card">
                <a href="<?= e($link) ?>" class="retreat-card__media" draggable="false">
                    <img src="<?= e(img((string) $r['image'])) ?>" alt="<?= e((string) $r['title']) ?>" width="297" height="447" loading="lazy" draggable="false">
                </a>
                <span class="retreat-card__tag"><?= e($tag) ?></span>
                <h3 class="retreat-card__title"><a href="<?= e($link) ?>" draggable="false"><?= e((string) $r['title']) ?></a></h3>
                <p class="retreat-card__text"><?= e($r['summary']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
