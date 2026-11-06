<?php
$pillars = [
    ['icon' => 'target',  'title' => 'Intentional',
     'text' => 'Each Wellness Escape is built around what human connection the traveller actually need to heal.'],
    ['icon' => 'hands',   'title' => 'Authentic',
     'text' => 'Reconnecting with nature and real human connection and traditions and cultures that are deeply rooted in each country. Authenticity is woven into every stage.'],
    ['icon' => 'leaf',    'title' => 'Transformative',
     'text' => 'Journeys that shift from within and it begins with a consultation on arrival, a plan you take home, and a call from us three days after you land back.'],
    ['icon' => 'globe',   'title' => 'Sustainable Transparency',
     'text' => 'Supporting the local communities with their crafts and their livelihood is a deeply personal practice we integrate into every journey we design.'],
];
$icons = [
    'target' => '<circle cx="11" cy="12" r="8"/><circle cx="11" cy="12" r="4.6"/><circle cx="11" cy="12" r="1.4" fill="currentColor"/><path d="M11 12 18.5 4.5M16 3.5h3v3"/>',
    'hands'  => '<path d="M2 9l4-3 4 2 3-2 5 1 4 3-3 4-5 3-3-1-4-2-3-2z"/><path d="M10 8l-3 3 2 1 3-2 4 3M9 13l3 2M7 15l2 1.5"/>',
    'leaf'   => '<circle cx="12" cy="12" r="9"/><path d="M7.5 12.5l3 3 6-7"/><path d="M14 5c3 0 5 2 5 5"/>',
    'globe'  => '<circle cx="12" cy="12" r="8.5"/><path d="M5 8c3 1 4 0 5 2s-1 3 0 5 3 2 3 4M15 4c-1 2 0 3 2 4s3 0 4 2M12 3.5c5 3 6 9 1 12"/>',
];
?>
<section class="margha-way" aria-labelledby="mw-heading">
    <div class="container">
        <p class="eyebrow eyebrow--tan text-center">The Mārghā Way</p>
        <h2 class="margha-way__title text-center" id="mw-heading">Every holiday we design at MĀRGHA is a journey rooted in healing. <br>That journey is a chapter in your life and it’s not simply a break</h2>
        <div class="pillars">
            <?php foreach ($pillars as $p): ?>
            <div class="pillar">
                <svg class="pillar__icon" viewBox="0 0 24 24" aria-hidden="true"><?= $icons[$p['icon']] ?></svg>
                <h3 class="pillar__title"><?= e($p['title']) ?></h3>
                <p class="pillar__text"><?= e($p['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
