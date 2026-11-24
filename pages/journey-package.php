<?php
/**
 * Journey Package page (e.g. The Women's Circle Getaway). $journey comes from journeys/journey.php.
 * Same layout family as the Wellness Package page (pk-* styles) plus a route map,
 * "Good to know", a places card and an enquiry form at the bottom.
 * Content: Admin › Journey Packages. Shared labels: Admin › Pages › Journeys › Journey page labels.
 */
/** @var array $journey */
$j      = $journey;
$v      = fn(string $k): string => trim((string) ($j[$k] ?? ''));
$name   = $v('title');
$depth  = JOURNEY_DEPTHS[$v('depth')] ?? '';
$nights = $v('nights');
$effort = $v('effort');
$hero   = $v('hero_image') !== '' ? $v('hero_image') : $v('image');
$journeysUrl = page_url('journeys');
$enquire     = url('enquire');
$note        = setting('jp_hero_note');
$badge  = $v('badge') !== '' ? $v('badge') : implode(' · ', array_filter([$depth, $nights !== '' ? preg_replace('/\D.*$/', '', $nights) . ' nights' : '']));

$facts  = text_items($v('facts')) ?: array_values(array_filter([['Depth', $depth], ['Nights', $nights], ['Effort', JOURNEY_EFFORTS[$effort] ?? ''], ['Group', $v('group_size')]], fn($f) => $f[1] !== ''));
$glance = text_items($v('at_a_glance'));
$stops  = text_items($v('route_stops'));
$days   = package_week($v('week'));
$inc    = text_lines($v('included'));
$exc    = text_lines($v('not_included'));
$good   = text_lines($v('good_to_know'));
$faq    = text_items($v('faq'));
$reads  = text_items($v('reading'), 3);
$fitFor = text_lines($v('fit_for'));
$fitNot = text_lines($v('fit_not'));
$related = get_journey_related($j);
$hasOverview = $v('headline') !== '' || $v('description') !== '' || $fitFor || $fitNot;

$words = [1 => 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen'];
$daysTitle = str_replace('{count}', $words[count($days)] ?? (string) count($days), setting('jk_days_title'));

/** Effort as three dots: ●○○ gentle, ●●○ active, ●●● demanding. */
$dots = function (string $effortKey): string {
    $n = array_search($effortKey, array_keys(JOURNEY_EFFORTS), true);
    $n = $n === false ? 0 : $n + 1;
    $html = '<span class="jr-dots" aria-hidden="true">';
    for ($i = 1; $i <= 3; $i++) $html .= '<i' . ($i <= $n ? ' class="on"' : '') . '></i>';
    return $html . '</span>';
};
$effortKeyFor = fn(string $label): string => (string) array_search(strtolower($label), array_map('strtolower', JOURNEY_EFFORTS), true);

$page_title       = $v('seo_title') !== '' ? $v('seo_title') : $name . ' — ' . ($nights !== '' ? preg_replace('/\D.*$/', '', $nights) . '-night ' : '') . 'journey in Sri Lanka | ' . APP_NAME;
$page_description = $v('seo_description') !== '' ? $v('seo_description') : ($v('hero_text') !== '' ? $v('hero_text') : $v('summary'));
$body_class       = 'page-wellness page-package page-journey';
$preload_image    = $hero;

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));
?>

<!-- Hero -->
<section class="pk-hero" aria-labelledby="pk-title">
    <?php if ($hero !== ''): ?><img class="pk-hero__bg" src="<?= e(img($hero)) ?>" alt="" fetchpriority="high"><?php endif; ?>
    <div class="container pk-hero__content">
        <p class="pk-crumbs"><a href="<?= e($journeysUrl) ?>">Journeys</a> <span aria-hidden="true">›</span> <a href="<?= e($journeysUrl . '#journeys-list') ?>">All journeys</a></p>
        <?php if ($badge !== ''): ?><p class="pk-badge"><?= e($badge) ?></p><?php endif; ?>
        <h1 class="pk-hero__title" id="pk-title"><?= e($name) ?></h1>
        <?php $heroText = $v('hero_text') !== '' ? $v('hero_text') : $v('summary'); if ($heroText !== ''): ?><p class="pk-hero__text"><?= e($heroText) ?></p><?php endif; ?>
    </div>
</section>

<!-- Key facts -->
<?php if ($facts): ?>
<div class="pk-facts" aria-label="Key facts">
    <dl class="container pk-facts__list">
        <?php foreach ($facts as [$label, $value]): ?><div class="pk-fact"><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div><?php endforeach; ?>
    </dl>
</div>
<?php endif; ?>

<!-- Section menu (sticky) -->
<nav class="pk-nav" aria-label="<?= e($name) ?> sections" data-pk-nav>
    <div class="container pk-nav__inner">
        <span class="pk-nav__name"><?= e($name) ?></span>
        <ul class="pk-nav__links">
            <?php if ($hasOverview): ?><li><a href="#overview"><?= e(setting('jk_nav_overview')) ?></a></li><?php endif; ?>
            <?php if ($stops): ?><li><a href="#route"><?= e(setting('jk_nav_route')) ?></a></li><?php endif; ?>
            <?php if ($days): ?><li><a href="#days"><?= e(setting('jk_nav_days')) ?></a></li><?php endif; ?>
            <?php if ($inc || $exc || $good): ?><li><a href="#included"><?= e(setting('jk_nav_included')) ?></a></li><?php endif; ?>
            <li><a href="#price"><?= e(setting('jk_nav_price')) ?></a></li>
            <?php if ($faq): ?><li><a href="#faq"><?= e(setting('jk_nav_faq')) ?></a></li><?php endif; ?>
        </ul>
        <a class="btn btn--rust pk-nav__cta" href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(setting('jk_nav_enquire')) ?></a>
    </div>
</nav>

<div class="pk-body">
    <div class="container pk-layout">

        <!-- ===== Main column ===== -->
        <div class="pk-main">

            <?php if ($hasOverview || $stops): ?>
            <section class="pk-block" id="overview">
                <?php if ($v('headline') !== ''): ?><h2 class="pk-title"><?= rich($v('headline')) ?></h2><?php endif; ?>
                <?= paras($v('description'), 'pk-lead') ?>

                <?php if ($stops):
                    // Route map: stops spaced along a gentle wave, joined by a dotted line.
                    $n = count($stops); $w = 600; $h = 150; $pad = 70;
                    $pts = [];
                    foreach ($stops as $i => $_) {
                        $x = $n === 1 ? $w / 2 : $pad + ($w - 2 * $pad) * $i / ($n - 1);
                        $y = $i % 2 === 0 ? 96 : 56;
                        $pts[] = [$x, $y];
                    }
                    $d = 'M' . $pts[0][0] . ' ' . $pts[0][1];
                    for ($i = 1; $i < $n; $i++) {
                        [$x0, $y0] = $pts[$i - 1]; [$x1, $y1] = $pts[$i];
                        $mx = ($x0 + $x1) / 2;
                        $d .= " C{$mx} " . ($y0 - 50) . " {$mx} " . ($y1 + 50) . " {$x1} {$y1}";
                    } ?>
                <figure class="jk-route" id="route" aria-label="Route: <?= e(implode(' → ', array_map(fn($s) => $s[0] . ($s[1] !== '' ? ' (' . $s[1] . ')' : ''), $stops))) ?>">
                    <svg viewBox="0 0 <?= $w ?> <?= $h ?>" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                        <?php if ($n > 1): ?><path d="<?= $d ?>" class="jk-route__line"/><?php endif; ?>
                        <?php foreach ($stops as $i => [$place, $stay]): [$x, $y] = $pts[$i]; ?>
                        <circle cx="<?= $x ?>" cy="<?= $y ?>" r="7" class="jk-route__dot"/>
                        <text x="<?= $x ?>" y="<?= $y + 28 ?>" text-anchor="middle" class="jk-route__label"><?= e($place) ?><?= $stay !== '' ? ' · ' . e($stay) : '' ?></text>
                        <?php endforeach; ?>
                    </svg>
                    <ol class="jk-route__list"><?php foreach ($stops as [$place, $stay]): ?><li><strong><?= e($place) ?></strong><?= $stay !== '' ? ' · ' . e($stay) : '' ?></li><?php endforeach; ?></ol>
                </figure>
                <?php endif; ?>

                <?php if ($fitFor || $fitNot): ?>
                <div class="pk-fit">
                    <?php if ($fitFor): ?><div class="pk-fit__box pk-fit__box--yes"><h3><?= e(setting('pk_fit_yes')) ?></h3><ul class="pk-bullets"><?php foreach ($fitFor as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                    <?php if ($fitNot): ?><div class="pk-fit__box pk-fit__box--no"><h3><?= e(setting('pk_fit_no')) ?></h3><ul class="pk-bullets"><?php foreach ($fitNot as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <?php if ($days): ?>
            <section class="pk-block" id="days">
                <h2 class="pk-title"><?= e($daysTitle) ?></h2>
                <ol class="pk-week">
                    <?php foreach ($days as [$d, $label, $title, $text, $tags]): ?>
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
            </section>
            <?php endif; ?>

            <?php if ($inc || $exc || $good): ?>
            <section class="pk-block" id="included">
                <div class="pk-incl">
                    <?php if ($inc): ?><div><h3 class="pk-sub"><?= e(setting('pk_incl_yes')) ?></h3><ul class="pk-bullets"><?php foreach ($inc as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                    <div>
                        <?php if ($exc): ?><h3 class="pk-sub"><?= e(setting('pk_incl_no')) ?></h3><ul class="pk-bullets"><?php foreach ($exc as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul><?php endif; ?>
                        <?php if ($good): ?><h3 class="pk-sub<?= $exc ? ' pk-sub--spaced' : '' ?>"><?= e(setting('jk_good_title')) ?></h3><ul class="pk-bullets"><?php foreach ($good as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul><?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($faq): ?>
            <section class="pk-block" id="faq">
                <h2 class="pk-title"><?= rich(setting('pk_faq_title')) ?></h2>
                <div class="pk-faq"><?php foreach ($faq as [$q, $a]): ?><details class="wp-faq__item"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details><?php endforeach; ?></div>
            </section>
            <?php endif; ?>

            <?php if ($reads): ?>
            <section class="pk-block">
                <h2 class="pk-title"><?= rich(setting('pk_read_title')) ?></h2>
                <div class="pk-read">
                    <?php foreach ($reads as [$label, $title, $link]): $tag = $link !== '' ? 'a' : 'div'; ?>
                    <<?= $tag ?> class="pk-read__card"<?= $link !== '' ? ' href="' . e(preg_match('#^https?://#', $link) ? $link : url($link)) . '"' : '' ?>><span class="pk-read__label"><?= e($label) ?></span><h3><?= e($title) ?></h3></<?= $tag ?>>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($related): ?>
            <section class="pk-block">
                <h2 class="pk-title"><?= rich(setting('jk_related_title')) ?></h2>
                <div class="pk-related">
                    <?php foreach ($related as $r): ?>
                    <a class="pk-related__card" href="<?= e($r['url']) ?>">
                        <span class="pk-related__media"><?php if (!empty($r['image'])): ?><img src="<?= e(img((string) $r['image'])) ?>" alt="" loading="lazy"><?php endif; ?></span>
                        <span class="pk-related__body">
                            <span class="pk-related__meta"><?= e((string) $r['meta']) ?></span>
                            <span class="pk-related__title"><?= e((string) $r['title']) ?></span>
                            <span class="pk-related__text"><?= e((string) $r['text']) ?></span>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <p class="pk-backlink"><a href="<?= e($journeysUrl . '#journeys-list') ?>"><svg width="16" height="12" viewBox="0 0 16 12" aria-hidden="true"><path d="M16 6H1.5M6.5 1l-5 5 5 5" fill="none" stroke="currentColor" stroke-width="1.3"/></svg> <?= e(setting('jk_back')) ?></a></p>
        </div>

        <!-- ===== Side column ===== -->
        <aside class="pk-side">
            <div class="pk-price" id="price">
                <p class="pk-price__eyebrow"><?= e(setting('pk_price_eyebrow')) ?></p>
                <p class="pk-price__amount"><span><?= e(setting('pk_price_from')) ?></span> <?= e($v('price_pp') !== '' ? $v('price_pp') : '[USD ———]') ?></p>
                <ul class="pk-price__notes">
                    <?php if ($v('price_basis') !== ''): ?><li><?= e($v('price_basis')) ?></li><?php endif; ?>
                    <?php foreach (text_lines($v('price_details')) as $l): ?><li><?= e($l) ?></li><?php endforeach; ?>
                </ul>
                <a class="btn btn--rust pk-price__btn" href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(setting('jk_btn_enquire')) ?></a>
                <a class="btn btn--ghost-light pk-price__btn" href="<?= e(page_url('wellness') . '#profile') ?>"><?= e(setting('jk_btn_profile')) ?></a>
                <p class="pk-price__note"><?= e($note) ?></p>
            </div>

            <?php if ($glance): ?>
            <div class="pk-card pk-glance">
                <h3><?= e(setting('pk_glance_title')) ?></h3>
                <dl><?php foreach ($glance as [$label, $value]): $ek = $effortKeyFor($value); ?><div><dt><?= e($label) ?></dt><dd><?= $ek !== '' ? $dots($ek) . ' ' : '' ?><?= e($value) ?></dd></div><?php endforeach; ?></dl>
            </div>
            <?php endif; ?>

            <?php if (!empty($j['places_show']) && $v('places') !== ''): ?>
            <div class="pk-card pk-practitioner">
                <p class="pk-card__eyebrow"><?= e(setting('jk_places_label')) ?></p>
                <p><?= e($v('places')) ?></p>
            </div>
            <?php endif; ?>

            <div class="pk-card pk-talk">
                <h3><?= e(setting('jk_talk_title')) ?></h3>
                <p><?= e(setting('jk_talk_text')) ?></p>
                <a href="<?= e($enquire) ?>" data-package="<?= e($name) ?>"><?= e(setting('jk_talk_link')) ?> <?= arrow_icon() ?></a>
            </div>
        </aside>
    </div>
</div>

<!-- Enquiry form ("Tell us where you are.") -->
<section class="jk-enquire" id="enquire-form" aria-labelledby="jk-enquire-title">
    <div class="container">
        <p class="eyebrow jk-enquire__eyebrow"><?= e(setting('jk_form_eyebrow')) ?></p>
        <h2 class="jk-enquire__title" id="jk-enquire-title"><?= rich(setting('jk_form_title')) ?></h2>
        <form class="jk-form" action="<?= e(url('api/enquire.php')) ?>" method="post" data-inline-enquiry novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="text" name="company" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="package" value="<?= e($name) ?>">
            <label class="sr-only" for="jk_name">Name</label><input id="jk_name" name="full_name" placeholder="Name" autocomplete="name" maxlength="120" required>
            <label class="sr-only" for="jk_email">Email address</label><input id="jk_email" name="email" type="email" placeholder="Email address" autocomplete="email" maxlength="190" required>
            <label class="sr-only" for="jk_where">Country · City</label><input id="jk_where" name="country" placeholder="Country · City" autocomplete="country-name" maxlength="80">
            <label class="sr-only" for="jk_dates">Preferred dates</label><input id="jk_dates" name="dates_text" placeholder="Preferred dates" maxlength="120">
            <label class="sr-only" for="jk_with">Travelling alone or with a friend?</label><input id="jk_with" name="companions" placeholder="Travelling alone or with a friend?" maxlength="160">
            <label class="sr-only" for="jk_hope">What do you hope to leave behind?</label><input id="jk_hope" name="hopes" placeholder="What do you hope to leave behind?" maxlength="300">
            <div class="jk-form__actions">
                <button class="btn jk-form__btn" type="submit"><?= e(setting('jk_form_button')) ?></button>
                <p class="jk-form__msg" role="status" aria-live="polite" data-thanks="<?= e(setting('jk_form_thanks')) ?>"></p>
            </div>
        </form>
    </div>
</section>

<?php partial('footer');
