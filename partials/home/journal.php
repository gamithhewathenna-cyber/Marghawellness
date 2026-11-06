<?php /** @var array $posts */ ?>
<section class="journal" aria-labelledby="journal-heading">
    <div class="container">
        <p class="eyebrow eyebrow--rust text-center"><?= e(setting('jn_eyebrow')) ?></p>
        <h2 class="journal__title text-center" id="journal-heading"><?= e(setting('jn_title')) ?></h2>

        <div class="journal__carousel">
            <button class="journal__arrow journal__arrow--prev" type="button" aria-label="Previous articles" data-journal-prev>
                <svg viewBox="0 0 14 34" aria-hidden="true"><path d="M12 1 2 17l10 16" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
            </button>
            <div class="journal__track" data-journal-track>
                <?php foreach ($posts as $p): ?>
                <article class="post-card">
                    <a class="post-card__media" href="<?= e(url('journal')) ?>#<?= e($p['slug']) ?>" tabindex="-1">
                        <img src="<?= e(img($p['image'])) ?>" alt="" width="328" height="394" loading="lazy">
                    </a>
                    <p class="post-card__cat"><?= e($p['category']) ?></p>
                    <h3 class="post-card__title"><a href="<?= e(url('journal')) ?>#<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                    <p class="post-card__excerpt"><?= e($p['excerpt']) ?>......<a class="post-card__more" href="<?= e(url('journal')) ?>#<?= e($p['slug']) ?>">read more&rarr;</a></p>
                </article>
                <?php endforeach; ?>
            </div>
            <button class="journal__arrow journal__arrow--next" type="button" aria-label="Next articles" data-journal-next>
                <svg viewBox="0 0 14 34" aria-hidden="true"><path d="m2 1 10 16L2 33" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
            </button>
        </div>
    </div>
</section>
