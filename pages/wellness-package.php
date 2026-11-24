<?php
/**
 * Wellness Package page (e.g. Arogya). $package comes from wellness/package.php.
 * Layout: hero → key facts strip → sticky section menu → main column + side column
 * (price, at a glance, practitioner, talk first). Every block is edited in
 * Admin › Wellness Packages; a block only shows when it has content.
 * Shared headings/labels: Admin › Pages › Wellness › Package page labels.
 */
/** @var array $package */
$p      = $package;
$v      = fn(string $k): string => trim((string) ($p[$k] ?? ''));
$name   = $v('title');
$depth  = wellness_category_name('depth', $v('depth'));
$path   = wellness_category_name('path', $v('path'));
$nights = $v('nights');
$nightsText = $nights !== '' ? str_replace('-', '–', $nights) . ' nights' : '';
$hero   = $v('hero_image') !== '' ? $v('hero_image') : $v('image');
$wellness = page_url('wellness');
$enquire  = url('enquire');
$note     = setting('wp_hero_note');

$facts  = text_items($v('facts')) ?: array_values(array_filter([['Depth', $depth], ['Nights', $nights], ['Path', $path]], fn($f) => $f[1] !== ''));
$glance = text_items($v('at_a_glance'));
$week   = package_week($v('week'));
$day    = text_items($v('sample_day'));
$inc    = text_lines($v('included'));
$exc    = text_lines($v('not_included'));
$add    = text_lines($v('addons'));
$faq    = text_items($v('faq'));
$reads  = text_items($v('reading'), 3);
$fitFor = text_lines($v('fit_for'));
$fitNot = text_lines($v('fit_not'));
$related = get_related_packages($p);
$hasOverview = $v('headline') !== '' || $v('description') !== '' || $fitFor || $fitNot;
[$prName, $prQual, $prLine] = array_pad(array_map('trim', explode('|', $v('practitioner'))), 3, '');

$page_title       = $v('seo_title') !== '' ? $v('seo_title') : $name . ' — ' . ($nights !== '' ? str_replace('-', '–', $nights) . '-night ' : '') . 'wellness programme | ' . APP_NAME;
$page_description = $v('seo_description') !== '' ? $v('seo_description') : $v('summary');
$body_class       = 'page-wellness page-package';
$preload_image    = $hero;

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));
?>

<!-- Hero -->
<section class="pk-hero" aria-labelledby="pk-title">
    <?php if ($hero !== ''): ?><img class="pk-hero__bg" src="<?= e(img($hero)) ?>" alt="" fetchpriority="high"><?php endif; ?>
    <div class="container pk-hero__content">
        <p class="pk-crumbs"><a href="<?= e($wellness) ?>">Wellness</a> <span aria-hidden="true">›</span> <a href="<?= e($wellness . '#wp-packages') ?>">Programmes</a></p>
        <p class="pk-badge"><?= e(implode(' · ', array_filter([$depth, $nightsText]))) ?></p>
        <h1 class="pk-hero__title" id="pk-title"><?= e($name) ?></h1>
        <?php if ($v('summary') !== ''): ?><p class="pk-hero__text"><?= e($v('summary')) ?></p><?php endif; ?>
    </div>
</section>

<!-- Key facts -->
<?php if ($facts): ?>
<div class="pk-facts" aria-label="Key facts">
    <dl class="container pk-facts__list">
        <?php foreach ($facts as [$label, $value]): ?>
        <div class="pk-fact"><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
        <?php endforeach; ?>
    </dl>
</div>
<?php endif; ?>

<!-- Section menu (sticky) -->
<nav class="pk-nav" aria-label="<?= e($name) ?> sections" data-pk-nav>
    <div class="container pk-nav__inner">
        <span class="pk-nav__name"><?= e($name) ?></span>
        <ul class="pk-nav__links">
            <?php if ($hasOverview): ?><li><a href="#overview"><?= e(setting('pk_nav_overview')) ?></a></li><?php endif; ?>
            <?php if ($week || $day): ?><li><a href="#week"><?= e(setting('pk_nav_week')) ?></a></li><?php endif; ?>
            <?php if ($inc || $exc || $add): ?><li><a href="#included"><?= e(setting('pk_nav_included')) ?></a></li><?php endif; ?>
            <li><a href="#price"><?= e(setting('pk_nav_price')) ?></a></li>
            <?php if ($faq): ?><li><a href="#faq"><?= e(setting('pk_nav_faq')) ?></a></li><?php endif; ?>
        </ul>
        <a class="btn btn--rust pk-nav__cta" href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(setting('pk_nav_enquire')) ?></a>
    </div>
</nav>

<div class="pk-body">
    <div class="container pk-layout">

        <!-- ===== Main column ===== -->
        <div class="pk-main">

            <?php if ($hasOverview): ?>
            <section class="pk-block" id="overview">
                <?php if ($v('headline') !== ''): ?><h2 class="pk-title"><?= rich($v('headline')) ?></h2><?php endif; ?>
                <?= paras($v('description'), 'pk-lead') ?>
                <?php if ($fitFor || $fitNot): ?>
                <div class="pk-fit">
                    <?php if ($fitFor): ?>
                    <div class="pk-fit__box pk-fit__box--yes">
                        <h3><?= e(setting('pk_fit_yes')) ?></h3>
                        <ul class="pk-bullets"><?php foreach ($fitFor as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($fitNot): ?>
                    <div class="pk-fit__box pk-fit__box--no">
                        <h3><?= e(setting('pk_fit_no')) ?></h3>
                        <ul class="pk-bullets"><?php foreach ($fitNot as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <?php if ($week || $day): ?>
            <section class="pk-block" id="week">
                <?php if ($week): ?>
                <h2 class="pk-title"><?= rich(setting('pk_week_title')) ?></h2>
                <ol class="pk-week">
                    <?php foreach ($week as [$d, $label, $title, $text, $tags]): ?>
                    <li class="pk-week__item">
                        <div class="pk-week__day"><span class="pk-week__num"><?= e($d) ?></span><?php if ($label !== ''): ?><span class="pk-week__label"><?= e($label) ?></span><?php endif; ?></div>
                        <div>
                            <h3><?= e($title) ?></h3>
                            <?php if ($text !== ''): ?><p><?= e($text) ?></p><?php endif; ?>
                            <?php if ($tags !== ''): ?><p class="pk-week__tags"><?= e($tags) ?></p><?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <?php endif; ?>

                <?php if ($day): ?>
                <h2 class="pk-title pk-title--spaced"><?= rich(setting('pk_day_title')) ?></h2>
                <ol class="pk-day">
                    <?php foreach ($day as [$time, $text]): ?><li><time><?= e($time) ?></time><span><?= e($text) ?></span></li><?php endforeach; ?>
                </ol>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <?php if ($inc || $exc || $add): ?>
            <section class="pk-block" id="included">
                <h2 class="pk-title"><?= rich(setting('pk_incl_title')) ?></h2>
                <div class="pk-incl">
                    <?php if ($inc): ?>
                    <div><h3 class="pk-sub"><?= e(setting('pk_incl_yes')) ?></h3><ul class="pk-bullets"><?php foreach ($inc as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>
                    <div>
                        <?php if ($exc): ?><h3 class="pk-sub"><?= e(setting('pk_incl_no')) ?></h3><ul class="pk-bullets"><?php foreach ($exc as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul><?php endif; ?>
                        <?php if ($add): ?><h3 class="pk-sub<?= $exc ? ' pk-sub--spaced' : '' ?>"><?= e(setting('pk_incl_add')) ?></h3><ul class="pk-bullets"><?php foreach ($add as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul><?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($faq): ?>
            <section class="pk-block" id="faq">
                <h2 class="pk-title"><?= rich(setting('pk_faq_title')) ?></h2>
                <div class="pk-faq">
                    <?php foreach ($faq as [$q, $a]): ?>
                    <details class="wp-faq__item"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($reads): ?>
            <section class="pk-block">
                <h2 class="pk-title"><?= rich(setting('pk_read_title')) ?></h2>
                <div class="pk-read">
                    <?php foreach ($reads as [$label, $title, $link]): $tag = $link !== '' ? 'a' : 'div'; ?>
                    <<?= $tag ?> class="pk-read__card"<?= $link !== '' ? ' href="' . e(preg_match('#^https?://#', $link) ? $link : url($link)) . '"' : '' ?>>
                        <span class="pk-read__label"><?= e($label) ?></span>
                        <h3><?= e($title) ?></h3>
                    </<?= $tag ?>>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($related): ?>
            <section class="pk-block">
                <h2 class="pk-title"><?= rich(setting('pk_related_title')) ?></h2>
                <div class="pk-related">
                    <?php foreach ($related as $r): $rl = package_url((string) $r['slug']);
                        $rn = trim((string) $r['nights']); ?>
                    <a class="pk-related__card" href="<?= e($rl) ?>">
                        <span class="pk-related__media"><?php if (!empty($r['image'])): ?><img src="<?= e(img((string) $r['image'])) ?>" alt="" loading="lazy"><?php endif; ?></span>
                        <span class="pk-related__body">
                            <span class="pk-related__meta"><?= e(implode(' · ', array_filter([wellness_category_name('depth', (string) $r['depth']), $rn !== '' ? str_replace('-', '–', $rn) . ' nights' : '']))) ?></span>
                            <span class="pk-related__title"><?= e((string) $r['title']) ?></span>
                            <span class="pk-related__text"><?= e((string) $r['summary']) ?></span>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <p class="pk-backlink"><a href="<?= e($wellness . '#wp-packages') ?>"><svg width="16" height="12" viewBox="0 0 16 12" aria-hidden="true"><path d="M16 6H1.5M6.5 1l-5 5 5 5" fill="none" stroke="currentColor" stroke-width="1.3"/></svg> <?= e(setting('pk_back')) ?></a></p>
        </div>

        <!-- ===== Side column ===== -->
        <aside class="pk-side">
            <div class="pk-price" id="price">
                <p class="pk-price__eyebrow"><?= e(setting('pk_price_eyebrow')) ?></p>
                <?php if ($v('price_from') !== ''): ?>
                <p class="pk-price__amount"><span><?= e(setting('pk_price_from')) ?></span> <?= e($v('price_from')) ?></p>
                <?php else: ?>
                <p class="pk-price__amount pk-price__amount--ask"><?= e(setting('pk_price_ask')) ?></p>
                <?php endif; ?>
                <?php if ($v('price_basis') !== '' || ($pd = text_lines($v('price_details')))): ?>
                <ul class="pk-price__notes">
                    <?php if ($v('price_basis') !== ''): ?><li><?= e($v('price_basis')) ?></li><?php endif; ?>
                    <?php foreach (text_lines($v('price_details')) as $l): ?><li><?= e($l) ?></li><?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <a class="btn btn--rust pk-price__btn" href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(str_replace('{name}', $name, setting('pk_btn_enquire'))) ?></a>
                <a class="btn btn--ghost-light pk-price__btn" href="<?= e($wellness . '#profile') ?>"><?= e(setting('pk_btn_profile')) ?></a>
                <p class="pk-price__note"><?= e($note) ?></p>
            </div>

            <?php if ($glance): ?>
            <div class="pk-card pk-glance">
                <h3><?= e(setting('pk_glance_title')) ?></h3>
                <dl><?php foreach ($glance as [$label, $value]): ?><div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div><?php endforeach; ?></dl>
            </div>
            <?php endif; ?>

            <?php if (!empty($p['practitioner_show']) && $prName !== ''): ?>
            <div class="pk-card pk-practitioner">
                <p class="pk-card__eyebrow"><?= e(setting('pk_practitioner_label')) ?></p>
                <h3><?= e($prName) ?></h3>
                <?php if ($prQual !== ''): ?><p class="pk-practitioner__qual"><?= e($prQual) ?></p><?php endif; ?>
                <?php if ($prLine !== ''): ?><p><?= e($prLine) ?></p><?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="pk-card pk-talk">
                <h3><?= e(setting('pk_talk_title')) ?></h3>
                <p><?= e(setting('pk_talk_text')) ?></p>
                <a href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(setting('pk_talk_link')) ?> <?= arrow_icon() ?></a>
            </div>
        </aside>
    </div>
</div>

<?php partial('footer');
