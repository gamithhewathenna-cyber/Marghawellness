<?php
/**
 * Wellness page — /wellness/ (wellness/index.php) or page.php?slug=wellness.
 * All text, lists and images: Admin › Pages › Wellness (settings groups wp_* in includes/settings.php).
 * "Enquire" links open the "Begin your journey" pop-up (partials/enquire.php).
 */
$page_title       = setting('wp_seo_title');
$page_description = setting('wp_seo_description');
$body_class       = 'page-wellness';
$preload_image    = setting('wp_hero_image');
$enquire          = url('enquire');   // any link to /enquire opens the enquiry pop-up

/** Lower-case keyword for filter matching: "Unplug & Reconnect" → "unplug-reconnect". */
$key = fn(string $s): string => trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-');
/** Night ranges → filter buckets: up to 5 = short, up to 10 = week, longer = long. */
$nightsBucket = function (string $n): string {
    preg_match('/\d+/', $n, $m);
    $first = (int) ($m[0] ?? 0);
    return $first <= 5 ? 'short' : ($first <= 10 ? 'week' : 'long');
};

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));
?>

<!-- 01 Hero -->
<section class="wp-hero" aria-labelledby="wp-hero-title">
    <img class="wp-hero__bg" src="<?= e(img(setting('wp_hero_image'))) ?>" alt="" fetchpriority="high">
    <div class="container wp-hero__content">
        <p class="eyebrow wp-hero__eyebrow"><?= e(setting('wp_hero_eyebrow')) ?></p>
        <h1 class="wp-hero__title" id="wp-hero-title"><?= rich(setting('wp_hero_title')) ?></h1>
        <?= paras(setting('wp_hero_text'), 'wp-hero__text') ?>
        <div class="wp-hero__actions">
            <a class="btn btn--rust" href="#profile"><?= e(setting('wp_hero_btn1')) ?></a>
            <a class="btn btn--ghost-light" href="<?= e($enquire) ?>"><?= e(setting('wp_hero_btn2')) ?></a>
        </div>
        <p class="wp-hero__note"><?= e(setting('wp_hero_note')) ?></p>
    </div>
</section>

<!-- 02 Fact bar -->
<?php if ($facts = setting_lines('wp_facts')): ?>
<section class="wp-facts" id="wp-facts" aria-label="At a glance">
    <ul class="container wp-facts__list">
        <?php foreach ($facts as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<!-- 03 Is it you? -->
<section class="wp-section wp-isit" id="wp-isit" aria-labelledby="wp-isit-title">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_isit_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-isit-title"><?= rich(setting('wp_isit_title')) ?></h2>
            <?= paras(setting('wp_isit_intro'), 'wp-lead') ?>
        </div>
        <div class="wp-isit__grid">
            <?php foreach (setting_items('wp_isit_items') as [$h, $t]): ?>
            <div class="wp-isit__item">
                <h3><?= e($h) ?></h3>
                <p><?= e($t) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="wp-isit__close"><?= rich(setting('wp_isit_close')) ?></p>
    </div>
</section>

<!-- 04 Three depths -->
<section class="wp-section wp-depths" id="wp-depths" aria-labelledby="wp-depths-title">
    <div class="container">
        <div class="wp-head wp-head--light">
            <p class="eyebrow eyebrow--tan"><?= e(setting('wp_depths_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-depths-title"><?= rich(setting('wp_depths_title')) ?></h2>
            <?= paras(setting('wp_depths_intro'), 'wp-lead') ?>
        </div>
        <ol class="wp-depths__grid">
            <?php foreach (setting_items('wp_depths_items', 4) as $i => [$name, $nights, $text, $programmes]): ?>
            <li class="wp-depth">
                <span class="wp-depth__num">0<?= $i + 1 ?></span>
                <h3 class="wp-depth__name"><?= e($name) ?></h3>
                <p class="wp-depth__nights"><?= e($nights) ?></p>
                <p class="wp-depth__text"><?= e($text) ?></p>
                <?php if ($programmes !== ''): ?><p class="wp-depth__progs"><?= e($programmes) ?></p><?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- 05 Wellness packages -->
<section class="wp-section wp-packages" id="wp-packages" aria-labelledby="wp-packages-title">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_pk_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-packages-title"><?= rich(setting('wp_pk_title')) ?></h2>
        </div>

        <form class="wp-filters" data-wp-filters aria-label="Filter programmes" onsubmit="return false">
            <label><span>Depth</span>
                <select name="depth"><option value="">All depths</option><option value="begin">Begin</option><option value="deepen">Deepen</option><option value="immerse">Immerse</option></select>
            </label>
            <label><span>Path</span>
                <select name="path"><option value="">All paths</option><option value="rest">Rest</option><option value="soul">Soul</option><option value="healing">Healing</option></select>
            </label>
            <label><span>For</span>
                <select name="for"><option value="">Everyone</option><option value="solo">Solo</option><option value="women">Women</option><option value="couples">Couples</option><option value="teams">Teams</option></select>
            </label>
            <label><span>Nights</span>
                <select name="nights"><option value="">Any length</option><option value="short">3 to 5 nights</option><option value="week">7 nights</option><option value="long">14 to 21 nights</option></select>
            </label>
        </form>

        <div class="wp-pk-grid" data-wp-programmes>
            <?php foreach (setting_items('wp_pk_items', 7) as [$name, $depth, $path, $for, $nights, $image, $desc]):
                $forKeys = implode(' ', array_map($key, array_filter(array_map('trim', explode(',', $for))))); ?>
            <article class="wp-pk" data-depth="<?= e($key($depth)) ?>" data-path="<?= e($key($path)) ?>" data-for="<?= e($forKeys) ?>" data-nights="<?= e($nightsBucket($nights)) ?>">
                <div class="wp-pk__media">
                    <?php if ($image !== ''): ?><img src="<?= e(img($image)) ?>" alt="" loading="lazy"><?php endif; ?>
                    <span class="wp-pk__depth"><?= e($depth) ?></span>
                </div>
                <div class="wp-pk__body">
                    <p class="wp-pk__meta"><?= e(implode(' · ', array_filter([$path, $nights !== '' ? str_replace('-', '–', $nights) . ' nights' : '', $for]))) ?></p>
                    <h3 class="wp-pk__title"><?= e($name) ?></h3>
                    <p class="wp-pk__text"><?= e($desc) ?></p>
                    <a class="wp-pk__link" href="<?= e($enquire) ?>"><?= e(setting('wp_pk_button')) ?> <?= arrow_icon() ?></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <p class="wp-pk-empty" data-wp-empty hidden><?= e(setting('wp_pk_empty')) ?> <a href="<?= e($enquire) ?>"><?= e(setting('wp_pk_empty_link')) ?></a></p>
    </div>
</section>

<!-- 06 How a stay is held -->
<section class="wp-section wp-held" id="wp-held" aria-labelledby="wp-held-title">
    <div class="container">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_held_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-held-title"><?= rich(setting('wp_held_title')) ?></h2>
        </div>
        <ol class="wp-held__steps">
            <?php foreach (setting_items('wp_held_items') as $i => [$h, $t]): ?>
            <li class="wp-held__step">
                <span class="wp-held__num">0<?= $i + 1 ?></span>
                <h3><?= e($h) ?></h3>
                <p><?= e($t) ?></p>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- 07 Why Sri Lanka -->
<section class="wp-why" id="wp-why" aria-labelledby="wp-why-title">
    <div class="wp-why__media"><img src="<?= e(img(setting('wp_why_image'))) ?>" alt="" loading="lazy"></div>
    <div class="wp-why__text">
        <p class="eyebrow eyebrow--tan"><?= e(setting('wp_why_eyebrow')) ?></p>
        <h2 class="wp-title" id="wp-why-title"><?= rich(setting('wp_why_title')) ?></h2>
        <ul class="wp-why__list">
            <?php foreach (setting_lines('wp_why_items') as $p): ?><li><?= e($p) ?></li><?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- 08 What you can rely on -->
<section class="wp-section wp-rely" id="wp-rely" aria-labelledby="wp-rely-title">
    <div class="container wp-rely__inner">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_rely_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-rely-title"><?= rich(setting('wp_rely_title')) ?></h2>
        </div>
        <ul class="wp-rely__list">
            <?php foreach (setting_lines('wp_rely_items') as $p): ?>
            <li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10.5"/><path d="M7.5 12.5l3 3 6-7"/></svg><span><?= e($p) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- 09 Practitioners & places — hidden until partner agreements are signed (Admin switch) -->
<?php if (setting('wp_people_show') === '1'): ?>
<section class="wp-section wp-people" id="wp-people">
    <div class="container wp-people__grid">
        <div><h2 class="wp-title"><?= rich(setting('wp_people_title1')) ?></h2><?= paras(setting('wp_people_text1'), 'wp-lead') ?></div>
        <div><h2 class="wp-title"><?= rich(setting('wp_people_title2')) ?></h2><?= paras(setting('wp_people_text2'), 'wp-lead') ?></div>
    </div>
</section>
<?php endif; ?>

<!-- 10 Bridge to Journeys -->
<section class="wp-bridge" id="wp-bridge" aria-labelledby="wp-bridge-title">
    <div class="container wp-bridge__inner">
        <h2 class="wp-bridge__title" id="wp-bridge-title"><?= rich(setting('wp_bridge_title')) ?></h2>
        <div>
            <?= paras(setting('wp_bridge_text'), 'wp-bridge__text') ?>
            <a class="btn btn--ghost-light btn--arrow" href="<?= e(url() . '#journeys-heading') ?>"><?= e(setting('wp_bridge_button')) ?> <?= arrow_icon() ?></a>
        </div>
    </div>
</section>

<!-- 11 Wellness Path Profile -->
<section class="wp-section wp-profile" id="profile" aria-labelledby="wp-profile-title">
    <div class="container wp-profile__inner">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_profile_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-profile-title"><?= rich(setting('wp_profile_title')) ?></h2>
            <?= paras(setting('wp_profile_text'), 'wp-lead') ?>
        </div>
        <form class="wp-profile__card" data-wp-profile action="<?= e(url('page.php')) ?>" method="get"><input type="hidden" name="slug" value="enquire">
            <p class="wp-profile__step">Question 1</p>
            <fieldset>
                <legend class="wp-profile__q"><?= e(setting('wp_profile_question')) ?></legend>
                <div class="wp-profile__options">
                    <?php foreach (setting_lines('eq_stages') as $i => $opt):
                        [$main, $sub] = array_pad(array_map('trim', explode('—', $opt, 2)), 2, ''); ?>
                    <label class="wp-profile__opt">
                        <input type="radio" name="stage" value="<?= e($opt) ?>"<?= $i === 0 ? ' required' : '' ?>>
                        <span><strong><?= e($main) ?></strong><?php if ($sub !== ''): ?><small><?= e($sub) ?></small><?php endif; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <p class="wp-profile__msg" data-wp-profile-msg role="status" aria-live="polite"></p>
            <button class="btn btn--rust btn--arrow" type="submit"><?= e(setting('wp_profile_button')) ?> <?= arrow_icon() ?></button>
        </form>
    </div>
</section>

<!-- 12 FAQs -->
<section class="wp-section wp-faq" id="wp-faq" aria-labelledby="wp-faq-title">
    <div class="container wp-faq__inner">
        <div class="wp-head">
            <p class="eyebrow eyebrow--rust"><?= e(setting('wp_faq_eyebrow')) ?></p>
            <h2 class="wp-title" id="wp-faq-title"><?= rich(setting('wp_faq_title')) ?></h2>
        </div>
        <div class="wp-faq__list">
            <?php foreach (setting_items('wp_faq_items') as [$q, $a]): ?>
            <details class="wp-faq__item">
                <summary><?= e($q) ?></summary>
                <p><?= e($a) ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('footer');
