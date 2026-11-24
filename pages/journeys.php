<?php
/**
 * Journeys page — /journeys/ (journeys/index.php) or page.php?slug=journeys.
 * Page text: Admin › Pages › Journeys (settings jp_*). Journey cards: Admin › Listings › Journeys.
 * "View journey" opens the enquiry pop-up with the journey's name until journey pages exist.
 */
$page_title       = setting('jp_seo_title');
$page_description = setting('jp_seo_description');
$body_class       = 'page-journeys';
$preload_image    = setting('jp_hero_image');
$enquire          = url('enquire');
$routes           = get_journey_routes();
$paths            = get_journey_paths();     // rest / soul / healing

/** Small line icons for the three paths. */
$pathIcon = fn(string $p): string => '<svg class="jr-icon" viewBox="0 0 24 24" aria-hidden="true">' . match ($p) {
    'rest'    => '<path d="M15.5 3.5a8.5 8.5 0 1 0 5 13.6A7 7 0 0 1 15.5 3.5z"/>',
    'healing' => '<path d="M12 21c-4-3-7-6-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 4-3 7-7 10z"/>',
    default   => '<path d="M12 3v18M3 12h18M6 6l12 12M18 6 6 18"/>',
} . '</svg>';
/** Effort as three dots: ●○○ gentle, ●●○ active, ●●● demanding. */
$dots = function (string $effort): string {
    $n = array_search($effort, array_keys(JOURNEY_EFFORTS), true);
    $n = $n === false ? 0 : $n + 1;
    $html = '<span class="jr-dots" aria-hidden="true">';
    for ($i = 1; $i <= 3; $i++) $html .= '<i' . ($i <= $n ? ' class="on"' : '') . '></i>';
    return $html . '</span>';
};

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));
?>

<!-- 1 Hero -->
<section class="wp-hero jr-hero" aria-labelledby="jr-hero-title">
    <img class="wp-hero__bg" src="<?= e(img(setting('jp_hero_image'))) ?>" alt="" fetchpriority="high">
    <div class="container wp-hero__content">
        <p class="eyebrow wp-hero__eyebrow"><?= e(setting('jp_hero_eyebrow')) ?></p>
        <h1 class="wp-hero__title" id="jr-hero-title"><?= rich(setting('jp_hero_title')) ?></h1>
        <?= paras(setting('jp_hero_text'), 'wp-hero__text') ?>
        <div class="wp-hero__actions">
            <a class="btn btn--rust" href="#journeys-list"><?= e(setting('jp_hero_btn1')) ?></a>
            <a class="btn btn--ghost-light" href="<?= e($enquire) ?>"><?= e(setting('jp_hero_btn2')) ?></a>
        </div>
        <p class="wp-hero__note"><?= e(setting('jp_hero_note')) ?></p>
    </div>
</section>

<!-- 2 Fact bar -->
<?php if ($facts = setting_items('jp_facts')): ?>
<div class="jr-facts" id="jr-facts">
    <ul class="container jr-facts__list">
        <?php foreach ($facts as [$t, $s]): ?>
        <li><span class="jr-facts__title"><?= e($t) ?></span><?php if ($s !== ''): ?><span class="jr-facts__sub"><?= e($s) ?></span><?php endif; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- 3 One island -->
<section class="wp-section jr-island" id="jr-island" aria-labelledby="jr-island-title">
    <div class="container">
        <div class="wp-head wp-head--light">
            <p class="eyebrow eyebrow--tan"><?= e(setting('jp_island_eyebrow')) ?></p>
            <h2 class="wp-title" id="jr-island-title"><?= rich(setting('jp_island_title')) ?></h2>
            <?= paras(setting('jp_island_text'), 'wp-lead') ?>
        </div>
        <div class="jr-island__grid">
            <?php foreach (setting_items('jp_island_items', 3) as [$label, $title, $text]): ?>
            <div class="jr-land">
                <p class="jr-land__label"><?= e($label) ?></p>
                <h3><?= e($title) ?></h3>
                <p><?= e($text) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4 Three depths -->
<section class="wp-section jr-depths" id="jr-depths" aria-labelledby="jr-depths-title">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('jp_depths_eyebrow')) ?></p>
            <h2 class="wp-title" id="jr-depths-title"><?= rich(setting('jp_depths_title')) ?></h2>
            <?= paras(setting('jp_depths_intro'), 'wp-lead') ?>
        </div>
        <ol class="jr-depths__grid">
            <?php foreach (setting_items('jp_depths_items', 4) as $i => [$name, $meta, $text, $might]): ?>
            <li class="jr-depth">
                <span class="jr-depth__num">0<?= $i + 1 ?></span>
                <h3><?= e($name) ?></h3>
                <p class="jr-depth__meta"><?= e($meta) ?></p>
                <p class="jr-depth__text"><?= e($text) ?></p>
                <?php if ($might !== ''): ?><p class="jr-depth__might"><strong>You might be:</strong> <?= e($might) ?></p><?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- 5 Journey cards -->
<section class="jr-list" id="journeys-list" aria-labelledby="jr-list-title">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('jp_list_eyebrow')) ?></p>
            <h2 class="wp-title" id="jr-list-title"><?= rich(setting('jp_list_title')) ?></h2>
        </div>
    </div>

    <div class="jr-filterbar" data-jr-filters>
        <div class="container jr-filterbar__inner">
            <div class="jr-filterbar__rows">
                <?php
                $groups = [
                    'depth'  => ['Depth', JOURNEY_DEPTHS, true],
                    'path'   => ['Path', $paths, false],
                    'style'  => ['Style', JOURNEY_STYLES, false],
                    'effort' => ['Effort', JOURNEY_EFFORTS, false],
                ];
                foreach ($groups as $g => [$label, $opts, $all]): ?>
                <div class="jr-chips" role="group" aria-label="<?= e($label) ?>" data-group="<?= $g ?>">
                    <span class="jr-chips__label"><?= e($label) ?></span>
                    <?php if ($all): ?><button type="button" class="jr-chip is-on" data-value="" aria-pressed="true">All</button><?php endif; ?>
                    <?php foreach ($opts as $k => $name): ?>
                    <button type="button" class="jr-chip" data-value="<?= e((string) $k) ?>" aria-pressed="false"><?php if ($g === 'path') echo $pathIcon((string) $k); ?><?= e(ucfirst((string) preg_replace('/^the | path$/i', '', (string) $name))) ?></button>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="jr-count"><span data-jr-count><?= count($routes) ?></span> <?= e(setting('jp_list_count')) ?></p>
        </div>
    </div>

    <div class="container">
        <div class="jr-grid">
            <?php foreach ($routes as $j):
                $depthKey = (string) ($j['depth'] ?? '');
                $pathKey  = (string) ($j['path'] ?? '');
                $effort   = (string) ($j['effort'] ?? '');
                ?>
            <article class="jr-card" data-depth="<?= e($depthKey) ?>" data-path="<?= e($pathKey) ?>" data-style="<?= e(str_replace(',', ' ', (string) ($j['style'] ?? ''))) ?>" data-effort="<?= e($effort) ?>">
                <a class="jr-card__media" href="<?= e(journey_url((string) $j['slug'])) ?>" tabindex="-1" aria-hidden="true">
                    <?php if (!empty($j['image'])): ?><img src="<?= e(img((string) $j['image'])) ?>" alt="" loading="lazy"><?php endif; ?>
                    <?php if (isset(JOURNEY_DEPTHS[$depthKey])): ?><span class="jr-card__pill"><?= e(JOURNEY_DEPTHS[$depthKey]) ?></span><?php endif; ?>
                </a>
                <div class="jr-card__body">
                    <?php if ($pathKey !== ''): ?><p class="jr-card__path"><?= $pathIcon($pathKey) ?><?= e(ucfirst((string) preg_replace('/^the | path$/i', '', $paths[$pathKey] ?? $pathKey))) ?></p><?php endif; ?>
                    <h3 class="jr-card__title"><a href="<?= e(journey_url((string) $j['slug'])) ?>"><?= e((string) $j['title']) ?></a></h3>
                    <?php if (!empty($j['tagline'])): ?><p class="jr-card__tag"><?= e((string) $j['tagline']) ?></p><?php endif; ?>
                    <?php if (!empty($j['route'])): ?><p class="jr-card__route"><?= e((string) $j['route']) ?></p><?php endif; ?>
                    <p class="jr-card__text"><?= e((string) $j['summary']) ?></p>
                    <dl class="jr-card__facts">
                        <?php if (!empty($j['nights'])): ?><div><dt>Nights</dt><dd><?= e((string) $j['nights']) ?></dd></div><?php endif; ?>
                        <?php if ($effort !== ''): ?><div><dt>Effort</dt><dd><?= $dots($effort) ?> <?= e(JOURNEY_EFFORTS[$effort] ?? ucfirst($effort)) ?></dd></div><?php endif; ?>
                        <?php if (!empty($j['group_size'])): ?><div><dt>Group</dt><dd><?= e((string) $j['group_size']) ?></dd></div><?php endif; ?>
                        <?php if (!empty($j['wellness'])): ?><div><dt>Wellness threaded</dt><dd><?= e((string) $j['wellness']) ?></dd></div><?php endif; ?>
                    </dl>
                    <div class="jr-card__foot">
                        <p class="jr-card__price">From <strong><?= e(trim((string) ($j['price_pp'] ?? '')) !== '' ? (string) $j['price_pp'] : '[USD ——]') ?></strong> pp</p>
                        <a class="jr-card__link" href="<?= e(journey_url((string) $j['slug'])) ?>"><?= e(setting('jp_list_button')) ?> →</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <!-- The Discovery Path (tailor-made) — always shown -->
            <article class="jr-card jr-card--discovery">
                <div class="jr-card__media">
                    <img src="<?= e(img(setting('jp_disc_image'))) ?>" alt="" loading="lazy">
                    <span class="jr-card__pill"><?= e(setting('jp_disc_label')) ?></span>
                </div>
                <div class="jr-card__body">
                    <h3 class="jr-card__title"><?= e(setting('jp_disc_title')) ?></h3>
                    <p class="jr-card__tag"><?= e(setting('jp_disc_tag')) ?></p>
                    <?= paras(setting('jp_disc_text'), 'jr-card__text') ?>
                    <a class="btn btn--rust jr-card__btn" href="<?= e($enquire) ?>" data-package="<?= e(setting('jp_disc_title')) ?>"><?= e(setting('jp_disc_button')) ?></a>
                </div>
            </article>
        </div>
        <p class="wp-pk-empty" data-jr-empty hidden><?= e(setting('jp_list_empty')) ?> <a href="<?= e($enquire) ?>" data-package="<?= e(setting('jp_disc_title')) ?>"><?= e(setting('jp_list_empty_link')) ?></a></p>
    </div>
</section>

<!-- 6 Which one is yours? -->
<section class="wp-section jr-compare" id="jr-compare" aria-labelledby="jr-compare-title">
    <div class="container">
        <p class="eyebrow eyebrow--rust"><?= e(setting('jp_cmp_eyebrow')) ?></p>
        <h2 class="wp-title" id="jr-compare-title"><?= rich(setting('jp_cmp_title')) ?></h2>
        <div class="jr-table-wrap">
            <table class="jr-table">
                <thead><tr><th scope="col"><span class="sr-only">Compare</span></th><th scope="col"><?= e(setting('jp_cmp_col1')) ?></th><th scope="col"><?= e(setting('jp_cmp_col2')) ?></th></tr></thead>
                <tbody>
                    <?php foreach (setting_items('jp_cmp_rows', 3) as [$label, $a, $b]): ?>
                    <tr><th scope="row"><?= e($label) ?></th><td data-label="<?= e(setting('jp_cmp_col1')) ?>"><?= e($a) ?></td><td data-label="<?= e(setting('jp_cmp_col2')) ?>"><?= e($b) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="jr-compare__close"><?= e(setting('jp_cmp_close')) ?></p>
    </div>
</section>

<!-- 7 FAQs -->
<section class="wp-section wp-faq" id="jr-faq" aria-labelledby="jr-faq-title">
    <div class="container wp-faq__inner">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('jp_faq_eyebrow')) ?></p>
            <h2 class="wp-title" id="jr-faq-title"><?= rich(setting('jp_faq_title')) ?></h2>
        </div>
        <div class="wp-faq__list">
            <?php foreach (setting_items('jp_faq_items') as [$q, $a]): ?>
            <details class="wp-faq__item"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('footer');
