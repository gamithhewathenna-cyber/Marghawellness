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
        // Full-screen enquiry form opened by every "Enquire Now" button (partials/enquire.php)
        'enquire' => ['label' => 'Enquiry form', 'fields' => [
            'eq_image'         => ['label' => 'Side photo (left side of the pop-up)', 'type' => 'image', 'default' => 'cta-yoga.jpg', 'help' => 'Portrait or square photo, about 1200px wide. The bottom fades into dark green behind the text.'],
            'eq_title'         => ['label' => 'Form title', 'type' => 'rich', 'default' => 'Begin your <em>journey</em>', 'help' => 'The word inside <em>…</em> is shown in rust.'],
            'eq_side_title'    => ['label' => 'Side heading — line 1', 'type' => 'text', 'default' => 'Tell us where you are.'],
            'eq_side_subtitle' => ['label' => 'Side heading — line 2 (gold)', 'type' => 'text', 'default' => 'We’ll shape the path.'],
            'eq_side_text'     => ['label' => 'Side text', 'type' => 'paras', 'rows' => 3, 'default' => 'No forms to fill in about places or prices. Just a few details about you, and a real person replies with where to begin.'],
            'eq_side_points'   => ['label' => 'Side bullet points (one per line)', 'type' => 'textarea', 'rows' => 4, 'default' => "A personal reply within a day, never a template\nReady-made journeys or fully customised holidays\nNo obligation, no payment, no pressure"],
            'eq_stages'        => ['label' => '“Where are you on your wellness journey?” choices (one per line)', 'type' => 'textarea', 'rows' => 8, 'default' => "I’m running on empty — Burnout, exhaustion, I need to stop\nI’m new to wellness travel — Curious, and not sure where to start\nI’ve done retreats before — Ready to go deeper, with more guidance\nI’m at a turning point — Seeking direction, purpose or a new chapter\nI’m healing from something — Want physician-led, guided care\nI’m planning for more than one — A partner, family, friends or a team\nI’m not sure yet — Help me work it out"],
            'eq_looking'       => ['label' => '“What are you looking for?” choices (one per line)', 'type' => 'textarea', 'rows' => 5, 'default' => "A ready-made MĀRGHĀ journey\nA fully customised holiday\nA group or team retreat\nI’m not sure yet"],
            'eq_button'        => ['label' => 'Button label', 'type' => 'text', 'default' => 'Begin the conversation'],
            'eq_note'          => ['label' => 'Small note under the button', 'type' => 'text', 'default' => 'We reply personally within a day. Your details stay with us and are never shared.'],
            'eq_success_title' => ['label' => 'Thank-you heading (after sending)', 'type' => 'text', 'default' => 'Thank you — your journey has begun.'],
            'eq_success_text'  => ['label' => 'Thank-you message', 'type' => 'paras', 'rows' => 3, 'default' => 'We’ve received your details. A real person from the MĀRGHĀ team will reply personally within a day.'],
        ]],
        /* ---------- Wellness page (/wellness — pages/wellness.php) ----------
           List fields: one item per line; " | " separates the parts of an item. */
        'wp_hero' => ['label' => 'Hero', 'fields' => [
            'wp_hero_image'   => ['label' => 'Background photo', 'type' => 'image', 'default' => 'cta-yoga.jpg', 'help' => 'Wide landscape photo, about 2400px wide.'],
            'wp_hero_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'MĀRGHĀ Wellness · Sri Lanka'],
            'wp_hero_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'For the body that has been <em>running on reserve</em>.'],
            'wp_hero_text'    => ['label' => 'Text', 'type' => 'paras', 'rows' => 3, 'default' => 'Rest is not the same as repair. MĀRGHĀ Wellness places you in one place, with partner physicians and practitioners who set your programme, so the stay changes something that lasts.'],
            'wp_hero_btn1'    => ['label' => 'Button 1 (goes to the Wellness Path Profile)', 'type' => 'text', 'default' => 'Take the Wellness Path Profile'],
            'wp_hero_btn2'    => ['label' => 'Button 2 (opens the enquiry form)', 'type' => 'text', 'default' => 'Speak to a specialist'],
            'wp_hero_note'    => ['label' => 'Small note under the buttons', 'type' => 'text', 'default' => 'A personal reply within a day · No obligation · Treatments set by our partner physicians and practitioners'],
        ]],
        'wp_facts' => ['label' => 'Fact bar', 'fields' => [
            'wp_facts' => ['label' => 'Facts (one per line)', 'type' => 'textarea', 'rows' => 5, 'default' => "You stay. One place, deeply.\n3 to 21 nights\nPractitioner-set, not chosen from a menu\nSri Lanka first, the Maldives to follow"],
        ]],
        'wp_isit' => ['label' => 'Is it you?', 'fields' => [
            'wp_isit_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Is it you?'],
            'wp_isit_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'What if you are not <em>tired</em>, but <em>depleted</em>?'],
            'wp_isit_intro'   => ['label' => 'Intro', 'type' => 'paras', 'rows' => 2, 'default' => 'Sleep fixes tired. Depleted is a different problem, and a change of view rarely touches it.'],
            'wp_isit_items'   => ['label' => 'Signs (one per line: Heading | text)', 'type' => 'textarea', 'rows' => 4, 'default' => "Waking up already behind. | The day starts in debt, and a long weekend does not clear it.\nThe holiday you need a holiday from. | You come home with a tan and the same tension.\nA body that stopped signalling. | You used to know when you were hungry, tired, done. Now you do not."],
            'wp_isit_close'   => ['label' => 'Closing line', 'type' => 'rich', 'default' => '<strong>Distraction is not repair.</strong> Something has to actually intervene: in the body, in the nervous system, in the pace of your days.'],
        ]],
        'wp_depths' => ['label' => 'Three depths', 'fields' => [
            'wp_depths_eyebrow'  => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Three depths'],
            'wp_depths_title'    => ['label' => 'Title', 'type' => 'rich', 'default' => 'Where are you on the path? <em>Start where you are.</em>'],
            'wp_depths_intro'    => ['label' => 'Intro', 'type' => 'paras', 'rows' => 2, 'default' => 'Every programme sits at one of three depths, so a first-timer is never sold a 21-night stay and a returning guest is never talked down to.'],
            'wp_depths_items'    => ['label' => 'Depths (one per line: Name | nights | text | programmes)', 'type' => 'textarea', 'rows' => 4, 'default' => "Begin | 3 to 5 nights | A first experience of practitioner-set care. Gentle, short, no knowledge needed. | Heal, Uplift, Unplug & Reconnect\nDeepen | 7 nights | A full, consultation-led week. Enough time for treatment to build and for you to feel the difference. | Arogya\nImmerse | 14 to 21 nights | Long stays for deep depletion, big transitions and committed practice, with silence and spiritual depth available. | Revive, Longevity"],
        ]],
        'wp_packages' => ['label' => 'Wellness packages', 'fields' => [
            'wp_pk_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Wellness programmes'],
            'wp_pk_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Six programmes. <em>One question:</em> how deep do you need to go?'],
            'wp_pk_button'  => ['label' => 'Card button label (opens the package page)', 'type' => 'text', 'default' => 'View programme'],
            'wp_pk_empty'   => ['label' => 'Message when no programme matches the filters', 'type' => 'text', 'default' => 'No programme matches.'],
            'wp_pk_empty_link' => ['label' => 'Link text after that message (opens the enquiry form)', 'type' => 'text', 'default' => 'Talk to a specialist'],
        ]],
        // Shared headings and labels used on every Wellness Package page (pages/wellness-package.php)
        'wp_pkpage' => ['label' => 'Package page labels', 'fields' => [
            'pk_nav_overview'  => ['label' => 'Menu bar — Overview', 'type' => 'text', 'default' => 'Overview'],
            'pk_nav_week'      => ['label' => 'Menu bar — Your week', 'type' => 'text', 'default' => 'Your week'],
            'pk_nav_included'  => ['label' => 'Menu bar — Included', 'type' => 'text', 'default' => 'Included'],
            'pk_nav_price'     => ['label' => 'Menu bar — Price', 'type' => 'text', 'default' => 'Price'],
            'pk_nav_faq'       => ['label' => 'Menu bar — FAQ', 'type' => 'text', 'default' => 'FAQ'],
            'pk_nav_enquire'   => ['label' => 'Menu bar — Enquire button', 'type' => 'text', 'default' => 'Enquire'],
            'pk_btn_enquire'   => ['label' => 'Enquire button (price card)', 'type' => 'text', 'default' => 'Enquire about {name}', 'help' => '{name} is replaced with the package name, e.g. “Enquire about Arogya”.'],
            'pk_btn_profile'   => ['label' => 'Path Profile button (price card)', 'type' => 'text', 'default' => 'Take the Path Profile'],
            'pk_fit_yes'       => ['label' => 'Fit — “for you” box title', 'type' => 'text', 'default' => 'This is for you if'],
            'pk_fit_no'        => ['label' => 'Fit — “not yet” box title', 'type' => 'text', 'default' => 'Perhaps not yet if'],
            'pk_week_title'    => ['label' => 'Your week — title', 'type' => 'rich', 'default' => 'Your week'],
            'pk_day_title'     => ['label' => 'Sample day — title', 'type' => 'rich', 'default' => 'A sample day'],
            'pk_incl_title'    => ['label' => 'Included — section title', 'type' => 'rich', 'default' => 'What’s included'],
            'pk_incl_yes'      => ['label' => 'Included — list title', 'type' => 'text', 'default' => 'Included'],
            'pk_incl_no'       => ['label' => 'Not included — list title', 'type' => 'text', 'default' => 'Not included'],
            'pk_incl_add'      => ['label' => 'Add-ons — list title', 'type' => 'text', 'default' => 'Add-ons'],
            'pk_faq_title'     => ['label' => 'Questions — title', 'type' => 'rich', 'default' => 'Questions'],
            'pk_read_title'    => ['label' => 'Read before you go — title', 'type' => 'rich', 'default' => 'Read before you go'],
            'pk_related_title' => ['label' => 'Related packages — title', 'type' => 'rich', 'default' => 'Step up, or step down.'],
            'pk_price_eyebrow' => ['label' => 'Price card — small heading', 'type' => 'text', 'default' => 'Investment'],
            'pk_price_from'    => ['label' => 'Price card — word before the price', 'type' => 'text', 'default' => 'from'],
            'pk_price_ask'     => ['label' => 'Price card — shown when no price is set', 'type' => 'text', 'default' => 'Price on request'],
            'pk_glance_title'  => ['label' => 'At a glance — title', 'type' => 'text', 'default' => 'At a glance'],
            'pk_practitioner_label' => ['label' => 'Practitioner card — small heading', 'type' => 'text', 'default' => 'Your practitioner'],
            'pk_talk_title'    => ['label' => '“Talk first” card — title', 'type' => 'text', 'default' => 'Talk first'],
            'pk_talk_text'     => ['label' => '“Talk first” card — text', 'type' => 'textarea', 'rows' => 3, 'default' => 'A 30-minute conversation with a Wellness Destination Specialist. No cost, no itinerary sent unless you ask.'],
            'pk_talk_link'     => ['label' => '“Talk first” card — link text (opens the enquiry form)', 'type' => 'text', 'default' => 'Book a conversation'],
            'pk_back'          => ['label' => 'Link back to all programmes', 'type' => 'text', 'default' => 'All wellness programmes'],
        ]],

        'wp_held' => ['label' => 'How a stay is held', 'fields' => [
            'wp_held_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'How a stay is held'],
            'wp_held_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Nobody should have to design <em>their own recovery</em>.'],
            'wp_held_items'   => ['label' => 'Steps (one per line: Heading | text)', 'type' => 'textarea', 'rows' => 4, 'default' => "The Conversation | Before anything is booked, we talk about what is actually going on.\nThe Match | Your Wellness Path Profile decides the practitioners, the place and the pace.\nThe Journey | Transfers, timings and one point of contact, all handled.\nThe Return | A follow-up, a practice to keep and a practitioner still reachable."],
        ]],
        'wp_why' => ['label' => 'Why Sri Lanka', 'fields' => [
            'wp_why_image'   => ['label' => 'Photo', 'type' => 'image', 'default' => 'destination-sigiriya.jpg'],
            'wp_why_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Why Sri Lanka for this'],
            'wp_why_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Ayurveda here is not a <em>spa menu</em>.'],
            'wp_why_items'   => ['label' => 'Points (one per line)', 'type' => 'textarea', 'rows' => 4, 'default' => "It is a living medical tradition with registered physicians, practised for generations.\nOcean, rainforest and highland sit within a few hours of each other, so a programme can hold the landscape it needs.\nNamed a top trending wellness destination for 2026 by BookRetreats, as reported by Time Out Asia."],
        ]],
        'wp_rely' => ['label' => 'What you can rely on', 'fields' => [
            'wp_rely_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Our promise'],
            'wp_rely_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'What you can <em>rely on</em>'],
            'wp_rely_items'   => ['label' => 'Promises (one per line)', 'type' => 'textarea', 'rows' => 4, 'default' => "Partner physicians and practitioners set every programme.\nA health form goes to your practitioner before you arrive.\nPrice per person, with everything included listed, and nothing hidden.\nOne named MĀRGHĀ contact from first conversation to the follow-up after you return."],
        ]],
        'wp_people' => ['label' => 'Practitioners & places', 'fields' => [
            'wp_people_show'   => ['label' => 'Show this section on the website', 'type' => 'bool', 'default' => '0', 'help' => 'Keep this off until partner agreements are signed.'],
            'wp_people_title1' => ['label' => 'Practitioners — title', 'type' => 'rich', 'default' => 'The practitioners <em>behind each programme</em>.'],
            'wp_people_text1'  => ['label' => 'Practitioners — text', 'type' => 'paras', 'rows' => 3, 'default' => 'Profiles of our partner physicians and practitioners will appear here.'],
            'wp_people_title2' => ['label' => 'Places — title', 'type' => 'rich', 'default' => 'The places <em>we stay</em>.'],
            'wp_people_text2'  => ['label' => 'Places — text', 'type' => 'paras', 'rows' => 3, 'default' => 'Our partner properties will appear here.'],
        ]],
        'wp_bridge' => ['label' => 'Bridge to Journeys', 'fields' => [
            'wp_bridge_title'  => ['label' => 'Title', 'type' => 'rich', 'default' => 'Not sure you need to lie down? <em>Some people need to wake up.</em>'],
            'wp_bridge_text'   => ['label' => 'Text', 'type' => 'paras', 'rows' => 2, 'default' => 'If rest is not the problem and life feels disconnected, our Journeys move you through the island instead.'],
            'wp_bridge_button' => ['label' => 'Button label', 'type' => 'text', 'default' => 'See MĀRGHĀ Journeys'],
        ]],
        'wp_profile' => ['label' => 'Wellness Path Profile', 'fields' => [
            'wp_profile_eyebrow'  => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Wellness Path Profile'],
            'wp_profile_title'    => ['label' => 'Title', 'type' => 'rich', 'default' => 'Five questions. <em>One honest suggestion.</em>'],
            'wp_profile_text'     => ['label' => 'Text', 'type' => 'paras', 'rows' => 3, 'default' => 'The Wellness Path Profile takes about three minutes. Nothing is booked, and you hear from a person, not a funnel.'],
            'wp_profile_question' => ['label' => 'Question', 'type' => 'text', 'default' => 'What brings you here?', 'help' => 'The answers are the “Where are you on your wellness journey?” choices from the Enquiry form, so the chosen answer carries straight into the form.'],
            'wp_profile_button'   => ['label' => 'Button label', 'type' => 'text', 'default' => 'Continue'],
        ]],
        'wp_faq' => ['label' => 'FAQs', 'fields' => [
            'wp_faq_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Questions'],
            'wp_faq_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Frequently asked <em>questions</em>'],
            'wp_faq_items'   => ['label' => 'Questions (one per line: Question | answer)', 'type' => 'textarea', 'rows' => 12, 'default' => "Do I have to be unwell to come? | No. Many guests come depleted rather than ill. Your practitioner will tell you honestly whether a programme suits you.\nIs this medical treatment? | Treatments are set and delivered by our partner physicians and practitioners, not by MĀRGHĀ. We arrange the journey. Nothing here is medical advice.\nHow long should I stay? | Three to five nights is a first reset. A week is where a programme begins to build. Longer stays are for deeper recovery. Your practitioner will advise.\nCan I come alone? | Yes. Most programmes are private and you are never expected to join a group.\nI have a health condition, take medication or am pregnant. | Tell us in the first conversation. Your health form goes to the practitioner, who may adjust or decline treatments.\nWhat does a day look like? | Slow and scheduled: gentle movement, treatment, food chosen for you, an empty hour, early sleep. See the sample day on each programme page.\nWhat does it cost? | Each page shows a price per person, what is included and what is not.\nWhen is the best time to go? | It depends on the property. The south-west coast is usually best from about December to March. We will advise by date.\nWhat if I need to change dates? | Our change policy is being finalised. Please ask us in your first conversation."],
        ]],
        'wp_seo' => ['label' => 'SEO', 'fields' => [
            'wp_seo_title'       => ['label' => 'Page title (browser tab and Google)', 'type' => 'text', 'max' => 120, 'default' => 'Wellness — Practitioner-led wellness stays in Sri Lanka | Mārghā Wellness'],
            'wp_seo_description' => ['label' => 'Description (Google)', 'type' => 'textarea', 'rows' => 3, 'max' => 300, 'default' => 'Rest is not the same as repair. Practitioner-set wellness programmes in Sri Lanka, from 3 to 21 nights, with partner physicians and one MĀRGHĀ contact throughout.'],
        ]],

        /* ---------- Journeys page (/journeys — pages/journeys.php) ----------
           List fields: one item per line; " | " separates the parts of an item. */
        'jp_hero' => ['label' => 'Hero', 'fields' => [
            'jp_hero_image'   => ['label' => 'Background photo', 'type' => 'image', 'default' => 'destination-sigiriya.jpg', 'help' => 'Wide landscape photo, about 2400px wide.'],
            'jp_hero_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'MĀRGHĀ Journeys · Sri Lanka'],
            'jp_hero_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Not a resort.<br>A route.'],
            'jp_hero_text'    => ['label' => 'Text', 'type' => 'paras', 'rows' => 3, 'default' => 'For the life that has gone quiet. Move through the island with local guides and naturalists, with wellness threaded through the days, so you come home awake.'],
            'jp_hero_btn1'    => ['label' => 'Button 1 (goes to the journey cards)', 'type' => 'text', 'default' => 'Find your journey'],
            'jp_hero_btn2'    => ['label' => 'Button 2 (opens the enquiry form)', 'type' => 'text', 'default' => 'Talk to a specialist'],
            'jp_hero_note'    => ['label' => 'Small note under the buttons', 'type' => 'text', 'default' => 'A personal reply within a day · No obligation · Treatments set by our partner physicians and practitioners'],
        ]],
        'jp_facts' => ['label' => 'Fact bar', 'fields' => [
            'jp_facts' => ['label' => 'Facts (one per line: Title | small text)', 'type' => 'textarea', 'rows' => 5, 'default' => "You move | The island, in sequence\n5 to 12 nights | Short escape to deep immersion\nLocal guides and naturalists | Naturalists, locals, facilitators\nSmall groups | Or entirely private"],
        ]],
        'jp_island' => ['label' => 'One island', 'fields' => [
            'jp_island_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'One island'],
            'jp_island_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Everything inside a<br>three-hour drive.'],
            'jp_island_text'    => ['label' => 'Text', 'type' => 'paras', 'rows' => 2, 'default' => 'Ocean, rainforest and highland sit close enough to move through in one journey, a closeness few destinations offer.'],
            'jp_island_items'   => ['label' => 'Landscapes (one per line: Small label | Title | text)', 'type' => 'textarea', 'rows' => 4, 'default' => "Coast | Ocean | Sunrise yoga, water, the slow end of the day.\nLowland | Rainforest | Forest therapy, river valleys, village meals.\nHighland | Tea country | Walking trails and mornings above the cloud.\nOpen sky | Dark sky | Camps where the night sky is actually visible."],
        ]],
        'jp_depths' => ['label' => 'Three depths', 'fields' => [
            'jp_depths_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Where are you on the path?'],
            'jp_depths_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Three depths of<br>immersion.'],
            'jp_depths_intro'   => ['label' => 'Intro', 'type' => 'paras', 'rows' => 2, 'default' => 'Choose by how much you want to be moved, not only by how long you have.'],
            'jp_depths_items'   => ['label' => 'Depths (one per line: Name | Effort · nights | text | You might be…)', 'type' => 'textarea', 'rows' => 4, 'default' => "First steps | Gentle · 5 nights | Short, supported and social. A way into slow travel without feeling out of your depth. | new to this, travelling with friends, wanting company.\nFinding footing | Active · 6 to 8 nights | Real walking days, a camp night, time off-grid. Enough effort to feel it, enough care to enjoy it. | past the sampler, ready for some discomfort that means something.\nGoing deeper | Demanding · 9 to 12 nights | Long, quiet journeys with teachers, ritual and silence. For those who want a turning point. | marking a milestone, drawn to spiritual practice, returning for more."],
        ]],
        'jp_list' => ['label' => 'Journey cards', 'fields' => [
            'jp_list_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'MĀRGHĀ Journeys'],
            'jp_list_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Six routes and <em>one tailor-made door</em>.'],
            'jp_list_button'  => ['label' => 'Card button label (opens the enquiry form until journey pages exist)', 'type' => 'text', 'default' => 'View journey'],
            'jp_list_count'   => ['label' => 'Counter text after the number of journeys', 'type' => 'text', 'default' => 'journeys + tailor-made'],
            'jp_list_empty'   => ['label' => 'Message when no journey matches the filters', 'type' => 'text', 'default' => 'No journey matches those filters yet.'],
            'jp_list_empty_link' => ['label' => 'Link text after that message (opens the enquiry form)', 'type' => 'text', 'default' => 'Ask us to design one'],
        ]],
        'jp_discovery' => ['label' => 'The Discovery Path (tailor-made)', 'fields' => [
            'jp_disc_label'  => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Tailor-made'],
            'jp_disc_title'  => ['label' => 'Title', 'type' => 'text', 'default' => 'The Discovery Path'],
            'jp_disc_tag'    => ['label' => 'Tagline', 'type' => 'text', 'default' => 'Your own MĀRGHĀ'],
            'jp_disc_text'   => ['label' => 'Text', 'type' => 'paras', 'rows' => 3, 'default' => 'Tell us what you are carrying and what you want to leave behind, and we design it from there.'],
            'jp_disc_button' => ['label' => 'Button label (opens the enquiry form)', 'type' => 'text', 'default' => 'Design my journey'],
            'jp_disc_image'  => ['label' => 'Photo', 'type' => 'image', 'default' => 'journey-maldives.jpg'],
        ]],
        'jp_compare' => ['label' => 'Which one is yours?', 'fields' => [
            'jp_cmp_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Which one is yours?'],
            'jp_cmp_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'The difference, plainly.'],
            'jp_cmp_col1'    => ['label' => 'Column 1 heading', 'type' => 'text', 'default' => 'MĀRGHĀ Wellness'],
            'jp_cmp_col2'    => ['label' => 'Column 2 heading', 'type' => 'text', 'default' => 'MĀRGHĀ Journeys'],
            'jp_cmp_rows'    => ['label' => 'Rows (one per line: Label | Wellness | Journeys)', 'type' => 'textarea', 'rows' => 6, 'default' => "The need | Something in the body needs repair | Something in the life needs waking\nThe shape | You stay. One place, deeply. | You move. The island, in sequence.\nWho leads | Partner physicians and practitioners | Naturalists, local guides, facilitators\nThe days | Slow, scheduled, adjusted daily | Active, exploratory, weather-led\nLength | 3 to 21 nights | 5 to 12 nights\nYou return | Restored | Changed"],
            'jp_cmp_close'   => ['label' => 'Closing line', 'type' => 'text', 'default' => 'Most people eventually do both. We usually suggest starting with the one you are resisting.'],
        ]],
        'jp_faq' => ['label' => 'FAQs', 'fields' => [
            'jp_faq_eyebrow' => ['label' => 'Small heading', 'type' => 'text', 'default' => 'Questions'],
            'jp_faq_title'   => ['label' => 'Title', 'type' => 'rich', 'default' => 'Frequently asked <em>questions</em>'],
            'jp_faq_items'   => ['label' => 'Questions (one per line: Question | answer)', 'type' => 'textarea', 'rows' => 8, 'default' => "How fit do I need to be? | Each journey shows an effort level. Gentle means walks of an hour or two; Active means half-day walks; Demanding means long days. Tell us your honest level and we will match it.\nCan I join a group alone? | Yes. Most guests do. Groups are small and hosted by a MĀRGHĀ team member.\nWhat if the weather turns? | Routes are weather-led. We carry alternatives and will adjust with your guide on the day.\nIs it safe? | Guides are local and experienced, vehicles are private, and we keep one named contact reachable throughout. [VERIFY: guide qualifications, first-aid cover, insurance requirements]\nIs wellness part of it or an add-on? | It is part of it: breathwork, silence, bodywork at the end of a hard day. Your guide and a practitioner shape it together.\nWhat is not included? | Flights, insurance and personal expenses. Each journey page lists the rest."],
        ]],
        'jp_seo' => ['label' => 'SEO', 'fields' => [
            'jp_seo_title'       => ['label' => 'Page title (browser tab and Google)', 'type' => 'text', 'max' => 120, 'default' => 'Journeys — Guided routes through Sri Lanka | Mārghā Wellness'],
            'jp_seo_description' => ['label' => 'Description (Google)', 'type' => 'textarea', 'rows' => 3, 'max' => 300, 'default' => 'Not a resort. A route. Move through Sri Lanka with local guides and naturalists, with wellness threaded through the days. 5 to 12 nights, small groups or private.'],
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

/** A setting edited as "one item per line", as a list of non-empty lines. */
function setting_lines(string $key): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/u', setting($key)) ?: []), 'strlen'));
}

/** Travel month choices for the enquiry form: this month + the next 23, as "March 2027". */
function enquiry_months(): array
{
    $months = [];
    $d = new DateTime('first day of this month');
    for ($i = 0; $i < 24; $i++, $d->modify('+1 month')) {
        $months[] = $d->format('F Y');
    }
    return $months;
}

/** Current value of a setting: saved value, else the default. */
function setting(string $key): string
{
    return settings_saved()[$key] ?? settings_defaults()[$key] ?? '';
}

/**
 * A list setting where each line has parts separated by " | ",
 * e.g. "Heading | text". Returns one array of trimmed parts per line,
 * padded to at least $parts entries so templates can index safely.
 */
function setting_items(string $key, int $parts = 2): array
{
    return array_map(
        fn($line) => array_pad(array_map('trim', explode('|', $line)), $parts, ''),
        setting_lines($key)
    );
}
