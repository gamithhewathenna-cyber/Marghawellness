<?php /** @var array $retreats */ ?>
<section class="wellness" aria-labelledby="wellness-heading">
    <div class="wellness__inner">
        <div class="wellness__intro">
            <p class="eyebrow eyebrow--rust">The Mārgha Wellness</p>
            <h2 class="wellness__title" id="wellness-heading">Wellness <em>Holidays &amp; Healing</em> <strong>designed</strong> specifically <em>around you</em>, not offered from a menu</h2>
            <div class="wellness__copy">
                <p>MĀRGHA Wellness is built around a single premise: healing that doesn't reach the whole person doesn't really last.</p>
                <p>Rooted in Ayurveda, detox and panchakarma, every programme addresses you physically, emotionally and spiritually - resetting a nervous system that’s forgotten how to switch off, and restoring sleep in a way a change of scenery rarely manages on its own.</p>
                <p>It happens through real time in nature and genuine human connection, not a checklist of treatments. You don't come back simply rested.You come back a more healed version of yourself.</p>
            </div>
            <a class="btn btn--rust btn--arrow" href="<?= e(url('wellness-holidays')) ?>">View all wellness holidays <?= arrow_icon() ?></a>
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
