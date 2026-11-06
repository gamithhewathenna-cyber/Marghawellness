<?php
$steps = [
    ['title' => 'Let us understand <strong>where you are</strong> and <em>where you want to be</em>',
     'body'  => ['Before anything is planned, we talk to you. The Conversation helps us figure out where you are emotionally and physically. You can let us know whether you want to break free from your daily routine or embark on a self-actualization path. We’ll start from where you are and work towards where you want to be.']],
    ['title' => 'Understanding the <em>perfect</em> <strong>Wellness</strong> <em>Match</em> for you',
     'body'  => ['Let us know the budget you have in your mind and the number of days you want to spend and the destination that speaks to you. Talk with us and let us know your expectations at the end of the journey. We build your Wellness Path Profile: constitution, history, capacity, what you’re really here to change. It decides the practitioners, the places and the pace.']],
    ['title' => 'We will curate this <strong>Wellness</strong> <em>Holiday</em> Just for <em>you</em>',
     'body'  => ['Wellness is not only about specific healthy practices, it is also about a serendipitous state of being in the right place at the right time.',
                 'We’ll help you navigate your first time wellness experience as a journey of transformation, a curated journey just for you and your wellbeing. Handled, in full. Practitioners, guides, transfers, timing, one point of contact the whole way through.']],
    ['title' => 'The Return &amp; <em>Continuation</em> of your <strong>Wellness</strong> <em>Journey</em>',
     'body'  => ['A follow-up, a practice to keep, a wellness specialist still reachable afterward to help you navigate your daily life with your new experiences and wellness learnings from your holiday.']],
];
?>
<section class="process" aria-labelledby="process-heading">
    <div class="container">
        <p class="eyebrow eyebrow--light">Let’s Discuss</p>
        <h2 class="process__title" id="process-heading">How your <em>Wellness</em> <br><em>Holiday</em> comes together</h2>
        <p class="process__question">Before we talk about places, where are you?</p>
        <p class="process__lead">Nobody Should Have to Design Their Own Healing And this is how a MĀRGHA journey takes shape, <br>whether you’re arriving as a guest, or sending someone to us.</p>
        <p class="process__lead">Every MĀRGHA journey, Wellness or Journeys, follows the same care underneath it, in four movements.</p>

        <ol class="steps">
            <?php foreach ($steps as $i => $s): ?>
            <li class="step">
                <span class="step__icon" aria-hidden="true">?</span>
                <span class="step__label">Step 0<?= $i + 1 ?></span>
                <h3 class="step__title"><?= $s['title'] ?></h3>
                <?php foreach ($s['body'] as $para): ?>
                <p class="step__text"><?= e($para) ?></p>
                <?php endforeach; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
