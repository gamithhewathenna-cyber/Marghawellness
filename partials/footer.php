</main>

<footer class="site-footer">
    <div class="container">
        <section class="newsletter" id="newsletter" aria-label="Stay in touch">
            <div class="newsletter__col">
                <p class="eyebrow eyebrow--light">Lorem ipsum dolor sit</p>
                <h2 class="newsletter__title">Lorem ipsum dolor sit amet consectetur. Egestas id tincidunt.</h2>
                <p class="newsletter__text">Lorem ipsum dolor sit amet consectetur. Vitae varius eu aliquet mi massa pulvinar mattis euismod. Tempor eget rhoncus dolor magna vitae tortor. Massa ornare.</p>
                <form class="subscribe-form" action="<?= e(url('api/subscribe.php')) ?>" method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="text" name="company" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <label class="sr-only" for="subscribeEmail">Your email address</label>
                    <input class="subscribe-form__input" id="subscribeEmail" type="email" name="email" placeholder="Your email address" required>
                    <button class="btn btn--rust subscribe-form__btn" type="submit">Subscribe</button>
                    <p class="subscribe-form__msg" role="status" aria-live="polite"><?= e($_SESSION['flash'] ?? '') ?></p><?php unset($_SESSION['flash']); ?>
                </form>
            </div>
            <div class="newsletter__col">
                <p class="eyebrow eyebrow--light">Lorem ipsum dolor sit</p>
                <h2 class="newsletter__title">Lorem ipsum dolor sit amet consectetur.</h2>
                <p class="newsletter__text">Lorem ipsum dolor sit amet consectetur. Vitae varius eu aliquet mi massa pulvinar mattis euismod. Tempor eget rhoncus dolor magna vitae tortor. Massa ornare.</p>
                <div class="newsletter__actions">
                    <a class="btn btn--rust newsletter__btn-wide" href="<?= e(SOCIAL_WHATSAPP) ?>" target="_blank" rel="noopener">Subscribe</a>
                    <a class="btn btn--rust newsletter__btn" href="<?= e(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener">Subscribe</a>
                </div>
            </div>
        </section>

        <div class="footer-main">
            <div class="footer-brand">
                <a class="logo logo--footer" href="<?= e(url()) ?>">MĀRGHĀ</a>
                <p class="footer-brand__tagline">Purposeful journeys through Sri Lanka<br>and the Maldives.</p>
                <div class="footer-social">
                    <a href="<?= e(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener">Instagram</a>
                    <a href="<?= e(SOCIAL_FACEBOOK) ?>" target="_blank" rel="noopener">Facebook</a>
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
                        <li><a href="<?= e(SOCIAL_WHATSAPP) ?>" target="_blank" rel="noopener">WhatsApp community</a></li>
                        <li><a href="<?= e(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener">@margawellness</a></li>
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
