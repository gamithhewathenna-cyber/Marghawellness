<section class="process" aria-labelledby="process-heading">
    <div class="container">
        <p class="eyebrow eyebrow--light"><?= e(setting('pr_eyebrow')) ?></p>
        <h2 class="process__title" id="process-heading"><?= rich(setting('pr_title')) ?></h2>
        <p class="process__question"><?= e(setting('pr_question')) ?></p>
        <p class="process__lead"><?= rich(setting('pr_lead1')) ?></p>
        <p class="process__lead"><?= rich(setting('pr_lead2')) ?></p>

        <ol class="steps">
            <?php for ($n = 1; $n <= 4; $n++): $png = trim(setting("step{$n}_icon")); ?>
            <li class="step">
                <?php if ($png !== ''): ?>
                <span class="step__icon step__icon--img" aria-hidden="true"><img src="<?= e(img($png)) ?>" alt="" width="26" height="26" loading="lazy"></span>
                <?php else: ?>
                <span class="step__icon" aria-hidden="true">?</span>
                <?php endif; ?>
                <span class="step__label">Step 0<?= $n ?></span>
                <h3 class="step__title"><?= rich(setting("step{$n}_title")) ?></h3>
                <?= paras(setting("step{$n}_text"), 'step__text') ?>
            </li>
            <?php endfor; ?>
        </ol>
    </div>
</section>
