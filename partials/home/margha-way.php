<?php
// Icons stay in code; titles and text are editable in Admin › Page text › The Mārghā Way.
$icons = [
    1 => '<circle cx="11" cy="12" r="8"/><circle cx="11" cy="12" r="4.6"/><circle cx="11" cy="12" r="1.4" fill="currentColor"/><path d="M11 12 18.5 4.5M16 3.5h3v3"/>',
    2 => '<path d="M2 9l4-3 4 2 3-2 5 1 4 3-3 4-5 3-3-1-4-2-3-2z"/><path d="M10 8l-3 3 2 1 3-2 4 3M9 13l3 2M7 15l2 1.5"/>',
    3 => '<circle cx="12" cy="12" r="9"/><path d="M7.5 12.5l3 3 6-7"/><path d="M14 5c3 0 5 2 5 5"/>',
    4 => '<circle cx="12" cy="12" r="8.5"/><path d="M5 8c3 1 4 0 5 2s-1 3 0 5 3 2 3 4M15 4c-1 2 0 3 2 4s3 0 4 2M12 3.5c5 3 6 9 1 12"/>',
];
?>
<section class="margha-way" aria-labelledby="mw-heading">
    <div class="container">
        <p class="eyebrow eyebrow--tan text-center"><?= e(setting('mw_eyebrow')) ?></p>
        <h2 class="margha-way__title text-center" id="mw-heading"><?= rich(setting('mw_title')) ?></h2>
        <div class="pillars">
            <?php foreach ($icons as $n => $icon): ?>
            <div class="pillar">
                <svg class="pillar__icon" viewBox="0 0 24 24" aria-hidden="true"><?= $icon ?></svg>
                <h3 class="pillar__title"><?= e(setting("pillar{$n}_title")) ?></h3>
                <?= paras(setting("pillar{$n}_text"), 'pillar__text') ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
