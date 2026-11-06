</main>

<footer class="site-footer">
    <div class="container">
        <section class="newsletter" id="newsletter" aria-label="Stay in touch">
            <div class="newsletter__col">
                <p class="eyebrow eyebrow--light"><?= e(setting('nl1_eyebrow')) ?></p>
                <h2 class="newsletter__title"><?= rich(setting('nl1_title')) ?></h2>
                <?= paras(setting('nl1_text'), 'newsletter__text') ?>
                <form class="subscribe-form" action="<?= e(url('api/subscribe.php')) ?>" method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="text" name="company" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <label class="sr-only" for="subscribeEmail">Your email address</label>
                    <input class="subscribe-form__input" id="subscribeEmail" type="email" name="email" placeholder="Your email address" required>
                    <button class="btn btn--rust subscribe-form__btn" type="submit"><?= e(setting('nl1_button')) ?></button>
                    <p class="subscribe-form__msg" role="status" aria-live="polite"><?= e($_SESSION['flash'] ?? '') ?></p><?php unset($_SESSION['flash']); ?>
                </form>
            </div>
            <div class="newsletter__col">
                <p class="eyebrow eyebrow--light"><?= e(setting('nl2_eyebrow')) ?></p>
                <h2 class="newsletter__title"><?= rich(setting('nl2_title')) ?></h2>
                <?= paras(setting('nl2_text'), 'newsletter__text') ?>
                <div class="newsletter__actions">
                    <a class="btn btn--rust newsletter__btn-wide" href="<?= e(setting('social_whatsapp')) ?>" target="_blank" rel="noopener"><?= e(setting('nl2_button1')) ?></a>
                    <a class="btn btn--rust newsletter__btn" href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener"><?= e(setting('nl2_button2')) ?></a>
                </div>
            </div>
        </section>

        <div class="footer-main">
            <div class="footer-brand">
                <a class="logo logo--footer" href="<?= e(url()) ?>">MĀRGHĀ</a>
                <p class="footer-brand__tagline"><?= rich(setting('footer_tagline')) ?></p>
                <div class="footer-social">
                    <a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener">Instagram</a>
                    <a href="<?= e(setting('social_facebook')) ?>" target="_blank" rel="noopener">Facebook</a>
                </div>
            </div>

            <div class="footer-links">
                <?php foreach (FOOTER_NAV as $heading => $slugs): ?>
                <div class="footer-links__col">
                    <h3><?= e($heading) ?></h3>
                    <ul>
                        <?php foreach ($slugs as $slug => $label): ?>
                        <li><a href="<?= e(url($slug)) ?>"><?= e($label) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
                <div class="footer-links__col">
                    <h3>Community</h3>
                    <ul>
                        <li><a href="<?= e(setting('social_whatsapp')) ?>" target="_blank" rel="noopener">WhatsApp community</a></li>
                        <li><a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener"><?= e(setting('instagram_handle')) ?></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Mārghā Wellness. All rights reserved.</p>
            <p class="footer-bottom__legal">
                <a href="<?= e(url('terms')) ?>">Terms and Conditions</a>
                <span aria-hidden="true"></span>
                <a href="<?= e(url('privacy')) ?>">Privacy Policy</a>
            </p>
        </div>
    </div>
</footer>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
