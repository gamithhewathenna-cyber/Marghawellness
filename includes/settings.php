<?php
declare(strict_types=1);

/**
 * Editable site text + settings.
 * --------------------------------------------------------------
 * The schema below drives the admin "Page text" screens. Each value is
 * stored in the `settings` table; until it is edited there (or if the
 * database is unavailable) the default below is used.
 *
 * Field types:
 *   text   – single line, plain text
 *   rich   – short text that may use <em>, <strong>, <br>
 *   paras  – longer text; a blank line starts a new paragraph
 *   url / email – validated links
 *   image  – an image in assets/images (upload or filename)
 */

function settings_schema(): array
{
    static $schema = null;
    if ($schema !== null) {
        return $schema;
    }
    $schema = [
        // Edited on its own admin page (admin/maintenance.php), not in the Page text tabs.
        'maintenance' => ['label' => 'Maintenance mode', 'own_page' => true, 'fields' => [
            'maintenance_enabled' => ['label' => 'Turn on maintenance mode', 'type' => 'bool', 'default' => '0',
                'help' => 'Visitors see the message below instead of the website. You stay signed in to the admin and can still view the real site.'],
            'maintenance_title'   => ['label' => 'Heading', 'type' => 'text', 'default' => 'We’ll be back soon'],
            'maintenance_message' => ['label' => 'Message', 'type' => 'paras', 'default' => "We’re making a few improvements to the Mārghā Wellness website and will be back shortly.\n\nIn the meantime, you can reach us at hello@margha.com or on Instagram @margawellness."],
        ]],
        'general' => ['label' => 'General & social', 'fields' => [
            'contact_email'      => ['label' => 'Contact email', 'type' => 'email', 'default' => CONTACT_EMAIL, 'help' => 'Where enquiry notifications will be sent.'],
            'social_instagram'   => ['label' => 'Instagram link', 'type' => 'url', 'default' => SOCIAL_INSTAGRAM],
            'instagram_handle'   => ['label' => 'Instagram handle (footer)', 'type' => 'text', 'default' => '@margawellness'],
            'social_facebook'    => ['label' => 'Facebook link', 'type' => 'url', 'default' => SOCIAL_FACEBOOK],
            'social_whatsapp'    => ['label' => 'WhatsApp community link', 'type' => 'url', 'default' => SOCIAL_WHATSAPP],
            'footer_tagline'     => ['label' => 'Footer tagline', 'type' => 'rich', 'default' => 'Purposeful journeys through Sri Lanka<br>and the Maldives.'],
        ]],
        'seo_home' => ['label' => 'SEO', 'fields' => [
            'seo_home_title'       => ['label' => 'Page title (browser tab and Google)', 'type' => 'text', 'max' => 120, 'default' => 'Mārghā Wellness — Wellness Holidays in Sri Lanka & the Maldives', 'help' => 'Aim for under 60 characters.'],
            'seo_home_description' => ['label' => 'Description (shown under the title on Google)', 'type' => 'textarea', 'rows' => 3, 'max' => 300, 'default' => 'Thoughtfully curated wellness holidays in Sri Lanka, for deep rest and meaningful transformation, designed so you come back different.', 'help' => 'Aim for 120–160 characters.'],
        ]],
        'seo_site' => ['label' => 'SEO', 'fields' => [
            'seo_default_description' => ['label' => 'Default Google description for pages without their own', 'type' => 'textarea', 'rows' => 3, 'max' => 300, 'default' => 'Thoughtfully curated wellness holidays in Sri Lanka and the Maldives, for deep rest and meaningful transformation.'],
        ]],
        'margha_way' => ['label' => 'The Mārghā Way', 'fields' => [
            'mw_eyebrow'    => ['label' => 'Small heading', 'type' => 'text', 'default' => 'The Mārghā Way'],
            'mw_title'      => ['label' => 'Title', 'type' => 'rich', 'default' => 'Every holiday we design at MĀRGHA is a journey rooted in healing. <br>That journey is a chapter in your life and it’s not simply a break'],
            'pillar1_icon'  => ['label' => 'Pillar 1 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). Leave empty to use the built-in line icon.'],
            'pillar1_title' => ['label' => 'Pillar 1 — title', 'type' => 'text', 'default' => 'Intentional'],
            'pillar1_text'  => ['label' => 'Pillar 1 — text', 'type' => 'paras', 'default' => 'Each Wellness Escape is built around what human connection the traveller actually need to heal.'],
            'pillar2_icon'  => ['label' => 'Pillar 2 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). Leave empty to use the built-in line icon.'],
            'pillar2_title' => ['label' => 'Pillar 2 — title', 'type' => 'text', 'default' => 'Authentic'],
            'pillar2_text'  => ['label' => 'Pillar 2 — text', 'type' => 'paras', 'default' => 'Reconnecting with nature and real human connection and traditions and cultures that are deeply rooted in each country. Authenticity is woven into every stage.'],
            'pillar3_icon'  => ['label' => 'Pillar 3 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). Leave empty to use the built-in line icon.'],
            'pillar3_title' => ['label' => 'Pillar 3 — title', 'type' => 'text', 'default' => 'Transformative'],
            'pillar3_text'  => ['label' => 'Pillar 3 — text', 'type' => 'paras', 'default' => 'Journeys that shift from within and it begins with a consultation on arrival, a plan you take home, and a call from us three days after you land back.'],
            'pillar4_icon'  => ['label' => 'Pillar 4 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). Leave empty to use the built-in line icon.'],
            'pillar4_title' => ['label' => 'Pillar 4 — title', 'type' => 'text', 'default' => 'Sustainable Transparency'],
            'pillar4_text'  => ['label' => 'Pillar 4 — text', 'type' => 'paras', 'default' => 'Supporting the local communities with their crafts and their livelihood is a deeply personal practice we integrate into every journey we design.'],
        ]],
        'wellness' => ['label' => 'Wellness holidays', 'fields' => [
            'wl_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'The Mārgha Wellness'],
            'wl_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Wellness <em>Holidays & Healing</em> <strong>designed</strong> specifically <em>around you</em>, not offered from a menu'],
            'wl_copy'    => ['label' => 'Text', 'type' => 'paras', 'default' => "MĀRGHA Wellness is built around a single premise: healing that doesn't reach the whole person doesn't really last.\n\nRooted in Ayurveda, detox and panchakarma, every programme addresses you physically, emotionally and spiritually - resetting a nervous system that’s forgotten how to switch off, and restoring sleep in a way a change of scenery rarely manages on its own.\n\nIt happens through real time in nature and genuine human connection, not a checklist of treatments. You don't come back simply rested.You come back a more healed version of yourself."],
            'wl_button'  => ['label' => 'Button label', 'type' => 'text', 'default' => 'View all wellness holidays'],
        ]],
        'process' => ['label' => 'How it comes together', 'fields' => [
            'pr_eyebrow'  => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Let’s Discuss'],
            'pr_title'    => ['label' => 'Title', 'type' => 'rich', 'default' => 'How your <em>Wellness</em> <br><em>Holiday</em> comes together'],
            'pr_question' => ['label' => 'Question line', 'type' => 'text', 'default' => 'Before we talk about places, where are you?'],
            'pr_lead1'    => ['label' => 'Intro paragraph 1', 'type' => 'rich', 'default' => 'Nobody Should Have to Design Their Own Healing And this is how a MĀRGHA journey takes shape, <br>whether you’re arriving as a guest, or sending someone to us.'],
            'pr_lead2'    => ['label' => 'Intro paragraph 2', 'type' => 'rich', 'default' => 'Every MĀRGHA journey, Wellness or Journeys, follows the same care underneath it, in four movements.'],
            'step1_icon'  => ['label' => 'Step 1 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). It sits inside the round cream badge. Leave empty to show the “?” placeholder.'],
            'step1_title' => ['label' => 'Step 1 — title', 'type' => 'rich', 'default' => 'Let us understand <strong>where you are</strong> and <em>where you want to be</em>'],
            'step1_text'  => ['label' => 'Step 1 — text', 'type' => 'paras', 'default' => 'Before anything is planned, we talk to you. The Conversation helps us figure out where you are emotionally and physically. You can let us know whether you want to break free from your daily routine or embark on a self-actualization path. We’ll start from where you are and work towards where you want to be.'],
            'step2_icon'  => ['label' => 'Step 2 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). It sits inside the round cream badge. Leave empty to show the “?” placeholder.'],
            'step2_title' => ['label' => 'Step 2 — title', 'type' => 'rich', 'default' => 'Understanding the <em>perfect</em> <strong>Wellness</strong> <em>Match</em> for you'],
            'step2_text'  => ['label' => 'Step 2 — text', 'type' => 'paras', 'default' => 'Let us know the budget you have in your mind and the number of days you want to spend and the destination that speaks to you. Talk with us and let us know your expectations at the end of the journey. We build your Wellness Path Profile: constitution, history, capacity, what you’re really here to change. It decides the practitioners, the places and the pace.'],
            'step3_icon'  => ['label' => 'Step 3 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). It sits inside the round cream badge. Leave empty to show the “?” placeholder.'],
            'step3_title' => ['label' => 'Step 3 — title', 'type' => 'rich', 'default' => 'We will curate this <strong>Wellness</strong> <em>Holiday</em> Just for <em>you</em>'],
            'step3_text'  => ['label' => 'Step 3 — text', 'type' => 'paras', 'default' => "Wellness is not only about specific healthy practices, it is also about a serendipitous state of being in the right place at the right time.\n\nWe’ll help you navigate your first time wellness experience as a journey of transformation, a curated journey just for you and your wellbeing. Handled, in full. Practitioners, guides, transfers, timing, one point of contact the whole way through."],
            'step4_icon'  => ['label' => 'Step 4 — icon (PNG)', 'type' => 'image', 'default' => '', 'help' => 'Upload a PNG icon (transparent background, about 128×128px). It sits inside the round cream badge. Leave empty to show the “?” placeholder.'],
            'step4_title' => ['label' => 'Step 4 — title', 'type' => 'rich', 'default' => 'The Return & <em>Continuation</em> of your <strong>Wellness</strong> <em>Journey</em>'],
            'step4_text'  => ['label' => 'Step 4 — text', 'type' => 'paras', 'default' => 'A follow-up, a practice to keep, a wellness specialist still reachable afterward to help you navigate your daily life with your new experiences and wellness learnings from your holiday.'],
        ]],
        'journeys' => ['label' => 'Journeys', 'fields' => [
            'jr_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Mārgha Journeys'],
            'jr_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Immersive experiences built to reconnect you with the world, <br>and with yourself.'],
            'jr_copy'    => ['label' => 'Text', 'type' => 'paras', 'default' => "Most travel skips the reason we actually go. MĀRGHA Journeys starts there, at the root of it: the need to connect, to see a world beyond ourselves, and in doing so, find our way back to who we really are.\n\nThese are immersive journeys through Sri Lanka's wildest and quietest corners , a night under a sky with no light for miles, days inside village\n\nlife, forest therapy and guided walks that put you back in your body and back in the present. At the centre of it is real disconnection - rom competition, from timelines, from the noise of ordinary life - and a chance to slow down, and heal"],
            'jr_filter_all' => ['label' => 'Filter: “all” button label', 'type' => 'text', 'default' => 'All regions'],
            'jr_button'  => ['label' => 'Button label', 'type' => 'text', 'default' => 'View all Journeys'],
            'jr_empty'   => ['label' => 'Message when a filter has no journeys', 'type' => 'text', 'default' => 'New journeys on this path are coming soon.'],
        ]],
        'destination' => ['label' => 'Destination', 'fields' => [
            'ds_eyebrow'   => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Your Destination'],
            'ds_title'     => ['label' => 'Title', 'type' => 'rich', 'default' => '<em>Sri Lanka:</em><br>From Mountains to Oceans <br>Move through the island & <br>Heal in it.'],
            'ds_copy'      => ['label' => 'Text', 'type' => 'paras', 'default' => "Sri Lanka is quietly becoming one of the world’s most compelling wellness destinations, and once you feel it, you understand why. The hills hold a calm that slows you before anything else does. The healing traditions reaches back over five thousand years, still practised by physicians rather than sold as a spa menu. The hospitality feels inherited, not trained.\n\nAnd ocean, rainforest and highland all sit within a few hours of each other, a density no other wellness destination quite matches. This is the island MARGHA calls home. Two paths, one place built for healing.\n\nAt MĀRGHA, we see it as a destination of effortless contrast and deep renewal, a place to travel through slowly, a destination that softens the body, clears the mind, and invites the soul to breathe."],
            'ds_button'    => ['label' => 'Button label', 'type' => 'text', 'default' => 'Get to know your destination'],
            'ds_image'     => ['label' => 'Image', 'type' => 'image', 'default' => 'destination-sigiriya.jpg'],
            'ds_image_alt' => ['label' => 'Image description (for accessibility)', 'type' => 'text', 'default' => 'Aerial view of Sigiriya rock fortress rising above the forest, Sri Lanka'],
        ]],
        'journal' => ['label' => 'Journal', 'fields' => [
            'jn_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Blog | News'],
            'jn_title'   => ['label' => 'Title', 'type' => 'text', 'default' => 'From the journal'],
        ]],
        'founders' => ['label' => 'Founders', 'fields' => [
            'f1_text'  => ['label' => 'Founder 1 — text', 'type' => 'paras', 'default' => "For twelve years I've built brands, written and made documentaries and commercials for Brands like Red Bull, Commercial Bank, Barista and a long run of travel and hospitality clients. For eight of those years the work was wellness travel. I spent them with Ayurva Traveller, four-time winner of Asia's Leading Wellness Travel Agency at the World Spa Awards, learning how a healing holiday is actually assembled and where it usually fails.\n\nWhat I kept seeing bothered me. Sri Lanka has practitioners the world would fly for, and almost nobody telling them properly. Guests arrive but there's no real navigation.\n\nMĀRGHĀ is my attempt at the alternative. With nearly a decade observing and working directly with Wellness Tourists, Wellness Brands and practitioners, and through my own experience of healing travel, we are curating holidays where you actually HEAL from WITHIN."],
            'f1_name'  => ['label' => 'Founder 1 — name', 'type' => 'text', 'default' => 'Fiyoni Pinto'],
            'f1_role'  => ['label' => 'Founder 1 — role', 'type' => 'text', 'default' => 'Founder & Managing Director'],
            'f1_image' => ['label' => 'Founder 1 — photo', 'type' => 'image', 'default' => 'founder-fiyoni.jpg'],
            'f2_text'  => ['label' => 'Founder 2 — text', 'type' => 'paras', 'default' => "For three years, I’ve lived on the ground side of wellness travel, building packages, holding logistics together, making sure every piece of a healing holiday actually fits. Along the way I’ve seen how easily a beautiful trip is promised, and how gently it can unravel in the details, a practitioner double booked, transport that doesn’t quite sync, timing that rushes the very stillness someone came searching for. It’s rarely a lack of care. It’s just that no one stitched the pieces together with enough Tenderness.\n\nMĀRGHĀ is where I get to do that stitching. My own quiet path to a calmer, healthier self has taught me what true restoration feels like, not just relaxed, but brought gently back to yourself. That’s the journey I love building: one held softly enough to simply let go, breathe, and Receive."],
            'f2_name'  => ['label' => 'Founder 2 — name', 'type' => 'text', 'default' => 'Senumi Evni'],
            'f2_role'  => ['label' => 'Founder 2 — role', 'type' => 'text', 'default' => 'Co-Founder'],
            'f2_image' => ['label' => 'Founder 2 — photo', 'type' => 'image', 'default' => 'founder-senumi.jpg'],
        ]],
        'cta' => ['label' => 'Call to action band', 'fields' => [
            'cta_eyebrow'  => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Begin'],
            'cta_title'    => ['label' => 'Title', 'type' => 'rich', 'default' => 'Your <strong>Wellness</strong> <em>Journey</em>'],
            'cta_subtitle' => ['label' => 'Title — second line', 'type' => 'text', 'default' => 'begins here'],
            'cta_button'   => ['label' => 'Button label', 'type' => 'text', 'default' => 'Enquire Now'],
            'cta_image'    => ['label' => 'Background image', 'type' => 'image', 'default' => 'cta-yoga.jpg'],
        ]],
        'footer' => ['label' => 'Footer newsletter', 'fields' => [
            'nl1_eyebrow' => ['label' => 'Left column — small heading', 'type' => 'text', 'default' => 'Lorem ipsum dolor sit'],
            'nl1_title'   => ['label' => 'Left column — title', 'type' => 'rich', 'default' => 'Lorem ipsum dolor sit amet consectetur. Egestas id tincidunt.'],
            'nl1_text'    => ['label' => 'Left column — text', 'type' => 'paras', 'default' => 'Lorem ipsum dolor sit amet consectetur. Vitae varius eu aliquet mi massa pulvinar mattis euismod. Tempor eget rhoncus dolor magna vitae tortor. Massa ornare.'],
            'nl1_button'  => ['label' => 'Left column — button label', 'type' => 'text', 'default' => 'Subscribe'],
            'nl2_eyebrow' => ['label' => 'Right column — small heading', 'type' => 'text', 'default' => 'Lorem ipsum dolor sit'],
            'nl2_title'   => ['label' => 'Right column — title', 'type' => 'rich', 'default' => 'Lorem ipsum dolor sit amet consectetur.'],
            'nl2_text'    => ['label' => 'Right column — text', 'type' => 'paras', 'default' => 'Lorem ipsum dolor sit amet consectetur. Vitae varius eu aliquet mi massa pulvinar mattis euismod. Tempor eget rhoncus dolor magna vitae tortor. Massa ornare.'],
            'nl2_button1' => ['label' => 'Right column — WhatsApp button label', 'type' => 'text', 'default' => 'Subscribe'],
            'nl2_button2' => ['label' => 'Right column — Instagram button label', 'type' => 'text', 'default' => 'Subscribe'],
        ]],
    ];
    return $schema;
}

/** key => default value, flattened from the schema. */
function settings_defaults(): array
{
    static $defaults = null;
    if ($defaults === null) {
        $defaults = [];
        foreach (settings_schema() as $group) {
            foreach ($group['fields'] as $key => $f) {
                $defaults[$key] = (string) $f['default'];
            }
        }
    }
    return $defaults;
}

/** Saved values from the database (loaded once per request). */
function settings_saved(bool $refresh = false): array
{
    static $values = null;
    if ($values === null || $refresh) {
        $values = [];
        foreach (Database::fetchAll('SELECT setting_key, setting_value FROM settings') ?? [] as $row) {
            if ($row['setting_value'] !== null) {
                $values[$row['setting_key']] = (string) $row['setting_value'];
            }
        }
    }
    return $values;
}

/** Current value of a setting: saved value, else the default. */
function setting(string $key): string
{
    return settings_saved()[$key] ?? settings_defaults()[$key] ?? '';
}
