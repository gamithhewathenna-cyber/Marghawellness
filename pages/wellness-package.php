<?php
/**
 * Wellness Package page (e.g. Arogya). $package comes from wellness/package.php.
 * Every section is edited in Admin › Wellness Packages; a section only shows when it has content.
 */
/** @var array $package */
$p     = $package;
$v     = fn(string $k): string => trim((string) ($p[$k] ?? ''));
$name  = $v('title');
$depth = wellness_category_name('depth', $v('depth'));
$path  = wellness_category_name('path', $v('path'));
$nights = $v('nights');
$eyebrow = implode(' · ', array_filter([$depth, $nights !== '' ? str_replace('-', ' to ', $nights) . ' nights' : '', $path]));
$hero    = $v('hero_image') !== '' ? $v('hero_image') : $v('image');
$wellness = page_url('wellness');
$enquire  = url('enquire');
$note     = setting('wp_hero_note');

$page_title       = $v('seo_title') !== '' ? $v('seo_title') : $name . ' — ' . ($nights !== '' ? str_replace('-', '–', $nights) . '-night ' : '') . 'wellness programme | ' . APP_NAME;
$page_description = $v('seo_description') !== '' ? $v('seo_description') : $v('summary');
$body_class       = 'page-wellness page-package';
$preload_image    = $hero;

/** The two call-to-action buttons (hero + price panel). */
$actions = function () use ($name, $enquire, $wellness): void { ?>
    <div class="pk-actions">
        <a class="btn btn--rust" href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(str_replace('{name}', $name, setting('pk_btn_enquire'))) ?></a>
        <a class="btn btn--ghost-light" href="<?= e($wellness . '#profile') ?>"><?= e(setting('pk_btn_profile')) ?></a>
    </div>
<?php };

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));
?>

<!-- Hero -->
<section class="wp-hero pk-hero" aria-labelledby="pk-title">
    <?php if ($hero !== ''): ?><img class="wp-hero__bg" src="<?= e(img($hero)) ?>" alt="" fetchpriority="high"><?php endif; ?>
    <div class="container wp-hero__content">
        <p class="pk-crumbs"><a href="<?= e($wellness) ?>">Wellness</a> <span aria-hidden="true">›</span> <a href="<?= e($wellness . '#wp-packages') ?>">Programmes</a></p>
        <p class="eyebrow wp-hero__eyebrow"><?= e($eyebrow) ?></p>
        <h1 class="wp-hero__title" id="pk-title"><?= e($name) ?></h1>
        <?php if ($v('summary') !== ''): ?><p class="wp-hero__text"><?= e($v('summary')) ?></p><?php endif; ?>
        <?php $actions(); ?>
        <p class="wp-hero__note"><?= e($note) ?></p>
    </div>
</section>

<!-- Introduction -->
<?php if ($v('headline') !== '' || $v('description') !== ''): ?>
<section class="wp-section pk-intro">
    <div class="container pk-intro__inner">
        <?php if ($v('headline') !== ''): ?><h2 class="wp-title"><?= rich($v('headline')) ?></h2><?php endif; ?>
        <div class="pk-intro__text"><?= paras($v('description'), 'wp-lead') ?></div>
    </div>
</section>
<?php endif; ?>

<!-- Fit -->
<?php $for = text_lines($v('fit_for')); $not = text_lines($v('fit_not')); if ($for || $not): ?>
<section class="wp-section pk-fit">
    <div class="container">
        <p class="eyebrow eyebrow--rust"><?= e(setting('pk_fit_eyebrow')) ?></p>
        <div class="pk-fit__grid">
            <?php if ($for): ?>
            <div class="pk-fit__col">
                <h2 class="pk-h"><?= e(setting('pk_fit_yes')) ?></h2>
                <ul class="pk-list pk-list--yes"><?php foreach ($for as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>
            <?php if ($not): ?>
            <div class="pk-fit__col">
                <h2 class="pk-h"><?= e(setting('pk_fit_no')) ?></h2>
                <ul class="pk-list pk-list--no"><?php foreach ($not as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Your week -->
<?php if ($week = text_items($v('week'), 3)): ?>
<section class="wp-section pk-week">
    <div class="container">
        <div class="wp-head wp-head--light">
            <p class="eyebrow eyebrow--tan"><?= e(setting('pk_week_eyebrow')) ?></p>
            <h2 class="wp-title"><?= rich(setting('pk_week_title')) ?></h2>
        </div>
        <ol class="pk-week__list">
            <?php foreach ($week as [$day, $title, $text]): ?>
            <li class="pk-week__item">
                <span class="pk-week__day"><?= e($day) ?></span>
                <div>
                    <h3><?= e($title) ?></h3>
                    <?php if ($text !== ''): ?><p><?= e($text) ?></p><?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
<?php endif; ?>

<!-- Sample day -->
<?php if ($day = text_items($v('sample_day'))): ?>
<section class="wp-section pk-day">
    <div class="container pk-day__inner">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('pk_day_eyebrow')) ?></p>
            <h2 class="wp-title"><?= rich(setting('pk_day_title')) ?></h2>
        </div>
        <ol class="pk-day__list">
            <?php foreach ($day as [$time, $text]): ?>
            <li><time><?= e($time) ?></time><span><?= e($text) ?></span></li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
<?php endif; ?>

<!-- Included / not included / add-ons -->
<?php $inc = text_lines($v('included')); $exc = text_lines($v('not_included')); $add = text_lines($v('addons')); if ($inc || $exc || $add): ?>
<section class="wp-section pk-incl">
    <div class="container">
        <p class="eyebrow eyebrow--rust"><?= e(setting('pk_incl_eyebrow')) ?></p>
        <div class="pk-incl__grid">
            <?php foreach ([[setting('pk_incl_yes'), $inc, 'yes'], [setting('pk_incl_no'), $exc, 'no'], [setting('pk_incl_add'), $add, 'plus']] as [$h, $items, $kind]): if (!$items) continue; ?>
            <div class="pk-incl__col">
                <h2 class="pk-h"><?= e($h) ?></h2>
                <ul class="pk-list pk-list--<?= $kind ?>"><?php foreach ($items as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Price panel -->
<section class="pk-price" id="price">
    <div class="container pk-price__inner">
        <div>
            <p class="eyebrow pk-price__eyebrow"><?= e(setting('pk_price_eyebrow')) ?></p>
            <?php if ($v('price_from') !== ''): ?>
            <p class="pk-price__amount"><span><?= e(setting('pk_price_from')) ?></span> <?= e($v('price_from')) ?></p>
            <?php if ($v('price_basis') !== ''): ?><p class="pk-price__basis"><?= e($v('price_basis')) ?></p><?php endif; ?>
            <?php else: ?>
            <p class="pk-price__amount pk-price__amount--ask"><?= e(setting('pk_price_ask')) ?></p>
            <?php endif; ?>
            <?php if ($details = text_lines($v('price_details'))): ?>
            <ul class="pk-price__details"><?php foreach ($details as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
        </div>
        <div>
            <?php $actions(); ?>
            <p class="wp-hero__note"><?= e($note) ?></p>
        </div>
    </div>
</section>

<!-- Read before you go -->
<?php if ($reads = text_items($v('reading'), 3)): ?>
<section class="wp-section pk-read">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('pk_read_eyebrow')) ?></p>
            <h2 class="wp-title"><?= rich(setting('pk_read_title')) ?></h2>
        </div>
        <div class="pk-read__grid">
            <?php foreach ($reads as [$label, $title, $link]): ?>
            <?php $tag = $link !== '' ? 'a' : 'div'; ?>
            <<?= $tag ?> class="pk-read__card"<?= $link !== '' ? ' href="' . e(preg_match('#^https?://#', $link) ? $link : url($link)) . '"' : '' ?>>
                <span class="pk-read__label"><?= e($label) ?></span>
                <h3><?= e($title) ?></h3>
                <?php if ($link !== ''): ?><span class="pk-read__more">Read <?= arrow_icon() ?></span><?php endif; ?>
            </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Back to all programmes -->
<section class="pk-back">
    <div class="container">
        <a class="pk-back__link" href="<?= e($wellness . '#wp-packages') ?>"><svg width="16" height="12" viewBox="0 0 16 12" aria-hidden="true"><path d="M16 6H1.5M6.5 1l-5 5 5 5" fill="none" stroke="currentColor" stroke-width="1.3"/></svg> <?= e(setting('pk_back')) ?></a>
    </div>
</section>

<?php partial('footer');
