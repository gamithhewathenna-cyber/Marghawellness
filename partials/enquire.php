<?php
/**
 * "Begin your journey" enquiry form.
 *   $mode = 'modal' (default) — full-screen overlay, opened by every "Enquire Now" link (assets/js/main.js)
 *   $mode = 'page'            — the same panel as a normal page (pages/enquire.php, used without JavaScript)
 * Text and choices: Admin › Pages › Header & footer › Enquiry form. Submits to api/enquire.php.
 */
$mode    = $mode ?? 'modal';
$isModal = $mode === 'modal';
$old     = $old ?? [];
$notice  = $notice ?? null;          // ['ok' => bool, 'message' => string] — page mode, after a no-JS submit
$sent    = !empty($notice['ok']);
$stages  = setting_lines('eq_stages');
$looking = setting_lines('eq_looking');
$v = fn(string $k): string => e((string) ($old[$k] ?? ''));
?>
<div class="enquire<?= $isModal ? '' : ' enquire--page' ?>" id="enquire"<?= $isModal ? ' role="dialog" aria-modal="true" aria-labelledby="enquireTitle" hidden' : '' ?>>
    <?php if ($isModal): ?>
    <button class="enquire__close" type="button" aria-label="Close" data-enquire-close>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
    </button>
    <?php endif; ?>

    <div class="enquire__panel">
        <aside class="enquire__side">
            <img class="enquire__img" src="<?= e(img(setting('eq_image'))) ?>" alt="" loading="lazy">
            <div class="enquire__side-text">
                <p class="enquire__side-title"><?= e(setting('eq_side_title')) ?><span><?= e(setting('eq_side_subtitle')) ?></span></p>
                <?= paras(setting('eq_side_text'), 'enquire__side-copy') ?>
                <?php if ($points = setting_lines('eq_side_points')): ?>
                <ul class="enquire__points">
                    <?php foreach ($points as $p): ?><li><?= e($p) ?></li><?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </aside>

        <div class="enquire__main">
            <div class="enquire__inner">
                <div class="enquire__done" data-enquire-done<?= $sent ? '' : ' hidden' ?> tabindex="-1">
                    <p class="eyebrow eyebrow--rust">Enquiry sent</p>
                    <h2 class="enquire__title"><?= e(setting('eq_success_title')) ?></h2>
                    <?= paras(setting('eq_success_text'), 'enquire__done-text') ?>
                    <?php if ($isModal): ?>
                    <button class="btn btn--rust" type="button" data-enquire-close>Close</button>
                    <?php else: ?>
                    <a class="btn btn--rust" href="<?= e(url()) ?>">Back to home</a>
                    <?php endif; ?>
                </div>

                <div data-enquire-body<?= $sent ? ' hidden' : '' ?>>
                <h2 class="enquire__title" id="enquireTitle"><?= rich(setting('eq_title')) ?></h2>

                <form class="enquire-form" action="<?= e(url('api/enquire.php')) ?>" method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="text" name="company" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <div class="ef-field">
                        <label for="ef_name">Full name</label>
                        <input id="ef_name" name="full_name" type="text" placeholder="Your name" autocomplete="name" maxlength="120" value="<?= $v('full_name') ?>" required>
                    </div>
                    <div class="ef-field">
                        <label for="ef_email">Email address</label>
                        <input id="ef_email" name="email" type="email" placeholder="you@example.com" autocomplete="email" maxlength="190" value="<?= $v('email') ?>" required>
                    </div>
                    <div class="ef-row">
                        <div class="ef-field">
                            <label for="ef_country">Country</label>
                            <input id="ef_country" name="country" type="text" placeholder="Where you live" autocomplete="country-name" maxlength="80" value="<?= $v('country') ?>">
                        </div>
                        <div class="ef-field">
                            <label for="ef_city">City</label>
                            <input id="ef_city" name="city" type="text" placeholder="Your city" autocomplete="address-level2" maxlength="80" value="<?= $v('city') ?>">
                        </div>
                    </div>
                    <div class="ef-field ef-field--select">
                        <label for="ef_stage">Where are you on your wellness journey?</label>
                        <select id="ef_stage" name="journey_stage">
                            <option value="">Choose what feels closest</option>
                            <?php foreach ($stages as $s): ?>
                            <option<?= ($old['journey_stage'] ?? '') === $s ? ' selected' : '' ?>><?= e($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ef-row">
                        <div class="ef-field ef-field--select">
                            <label for="ef_looking">What are you looking for?</label>
                            <select id="ef_looking" name="looking_for">
                                <option value="">Select one</option>
                                <?php foreach ($looking as $l): ?>
                                <option<?= ($old['looking_for'] ?? '') === $l ? ' selected' : '' ?>><?= e($l) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="ef-field ef-field--select">
                            <label for="ef_month">When would you like to travel?</label>
                            <select id="ef_month" name="travel_month">
                                <option value="">Month and year</option>
                                <?php foreach (enquiry_months() as $m): ?>
                                <option<?= ($old['travel_month'] ?? '') === $m ? ' selected' : '' ?>><?= e($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="ef-check"><input type="checkbox" name="dates_flexible" value="1"<?= !empty($old['dates_flexible']) ? ' checked' : '' ?>> My dates are flexible</label>
                        </div>
                    </div>

                    <div class="enquire-form__actions">
                        <button class="btn btn--rust enquire-form__submit" type="submit"><?= e(setting('eq_button')) ?></button>
                        <p class="enquire-form__note"><?= e(setting('eq_note')) ?></p>
                        <p class="enquire-form__msg<?= $notice && !$sent ? ' is-error' : '' ?>" role="status" aria-live="polite"><?= $notice && !$sent ? e($notice['message']) : '' ?></p>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
