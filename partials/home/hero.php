<?php /** @var array $slides */ ?>
<section class="hero" aria-roledescription="carousel" aria-label="Featured">
    <div class="hero__slides" data-hero>
        <?php foreach ($slides as $i => $s): ?>
        <div class="hero__slide<?= $i === 0 ? ' is-active' : '' ?>" aria-roledescription="slide" aria-label="<?= $i + 1 ?> of <?= count($slides) ?>"<?= $i ? ' aria-hidden="true"' : '' ?>>
            <img class="hero__bg" src="<?= e(img($s['image'])) ?>" alt="" width="1439" height="806"<?= $i ? ' loading="lazy"' : ' fetchpriority="high"' ?>>
            <div class="container hero__content">
                <p class="eyebrow eyebrow--tan hero__eyebrow"><?= e($s['eyebrow']) ?></p>
                <?php if ($i === 0): ?>
                <h1 class="hero__title"><?= rich($s['title_html']) ?></h1>
                <?php else: ?>
                <h2 class="hero__title"><?= rich($s['title_html']) ?></h2>
                <?php endif; ?>
                <p class="hero__text"><?= e($s['subtitle']) ?></p>
                <a class="btn btn--rust hero__btn" href="<?= e(url($s['cta_url'])) ?>"><?= e($s['cta_label']) ?></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="hero__arrow hero__arrow--prev" type="button" aria-label="Previous slide" data-hero-prev>
        <svg viewBox="0 0 14 34" aria-hidden="true"><path d="M12 1 2 17l10 16" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
    </button>
    <button class="hero__arrow hero__arrow--next" type="button" aria-label="Next slide" data-hero-next>
        <svg viewBox="0 0 14 34" aria-hidden="true"><path d="m2 1 10 16L2 33" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
    </button>
</section>
