<?php /** @var array $journeys  @var array $paths */ ?>
<section class="journeys" aria-labelledby="journeys-heading">
    <div class="container">
        <p class="eyebrow eyebrow--rust text-center"><?= e(setting('jr_eyebrow')) ?></p>
        <h2 class="journeys__title text-center" id="journeys-heading"><?= rich(setting('jr_title')) ?></h2>
        <div class="journeys__copy text-center">
            <?= paras(setting('jr_copy')) ?>
        </div>

        <div class="filters" role="tablist" aria-label="Filter journeys">
            <button class="filter is-active" type="button" role="tab" aria-selected="true" data-filter="all"><?= e(setting('jr_filter_all')) ?></button>
            <?php foreach ($paths as $key => $label): ?>
            <button class="filter" type="button" role="tab" aria-selected="false" data-filter="<?= e($key) ?>"><?= e($label) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="journey-grid" data-journey-grid>
            <?php foreach ($journeys as $j): ?>
            <article class="journey-card" data-path="<?= e($j['path']) ?>">
                <a class="journey-card__media" href="<?= e(url('journeys')) ?>#<?= e($j['slug']) ?>">
                    <img src="<?= e(img($j['image'])) ?>" alt="<?= e($j['title']) ?>" width="387" height="387" loading="lazy">
                </a>
                <p class="journey-card__price">From $<?= e((string) (int) $j['price_from']) ?>/night</p>
                <div class="journey-card__head">
                    <h3 class="journey-card__title"><?= e($j['title']) ?></h3>
                    <a class="journey-card__view" href="<?= e(url('journeys')) ?>#<?= e($j['slug']) ?>" aria-label="View <?= e($j['title']) ?>">View <svg width="12" height="10" viewBox="0 0 16 12" aria-hidden="true"><path d="M0 6h14.5M9.5 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></a>
                </div>
                <p class="journey-card__text"><?= e($j['summary']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <p class="journeys__empty" hidden><?= e(setting('jr_empty')) ?></p>

        <div class="text-center">
            <a class="btn btn--green btn--arrow journeys__btn" href="<?= e(url('journeys')) ?>"><?= e(setting('jr_button')) ?> <?= arrow_icon() ?></a>
        </div>
    </div>
</section>
