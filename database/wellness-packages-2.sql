-- MĀRGHĀ Wellness — Wellness Package page, new layout (key facts, at a glance, questions, related, practitioner)
-- Run ONCE, AFTER database/wellness-packages.sql. No data is deleted.
-- If you see "Duplicate column name 'facts'", this update has already been applied.

SET NAMES utf8mb4;

ALTER TABLE wellness_programmes
    ADD COLUMN facts             TEXT NULL AFTER summary,
    ADD COLUMN at_a_glance       TEXT NULL AFTER facts,
    ADD COLUMN faq               TEXT NULL AFTER reading,
    ADD COLUMN related           VARCHAR(255) NULL AFTER faq,
    ADD COLUMN practitioner_show TINYINT(1) NOT NULL DEFAULT 0 AFTER related,
    ADD COLUMN practitioner      VARCHAR(300) NULL AFTER practitioner_show;

-- Arogya: first package, with the new page content (only fills empty fields)
UPDATE wellness_programmes SET
    sort_order  = 0,
    facts       = IF(facts IS NULL OR facts = '', 'Depth | Deepen\nNights | 7\nPath | Healing\nShape | You stay, one place\nLed by | Partner Ayurvedic physician\nSeason | [VERIFY]', facts),
    at_a_glance = IF(at_a_glance IS NULL OR at_a_glance = '', 'Nights | 7\nGroup | Private, 1–2 guests\nDates | Rolling · [TBC]\nWhere | [Partner property]\nTreatments by | Partner physician', at_a_glance),
    faq         = IF(faq IS NULL OR faq = '', 'Is this suitable if I’m new to wellness travel? | Yes. Arogya begins with a consultation, so no prior knowledge is needed. If a shorter first step suits you better, we may suggest a Begin programme such as Heal.\nWhat if I have a health condition or take medication? | Tell us in the first conversation. Your health form goes to the practitioner, who may adjust or decline treatments.\nCan I travel solo? | Yes. Arogya is private, and you are never expected to join a group.\nWhat happens if I need to change dates? | Our change policy is being finalised. Please ask us in your first conversation.', faq),
    related     = IF(related IS NULL OR related = '', 'heal, revive', related),
    week        = IF(week IS NULL OR week = '' OR week LIKE 'Day 1 | Arrive | Private transfer%',
                     '1 | Arrive | Arrive and be received | Private transfer, a quiet room, a first conversation with your partner physician. Nothing is scheduled that you did not agree to. | Consultation · assessment\n2 | Settle | The programme is set | Your physician sets the programme from the consultation. Gentle movement, food built for you, early sleep. | Practitioner-set\n3–5 | Treat | Treatment builds | Daily bodywork and therapies, adjusted each morning according to how you responded. | Adjusted daily\n6 | Deepen | Stillness | A lighter day: breathwork, meditation, silence in the garden. The empty hour that feels harder than it sounds. | Breathwork · silence\n7 | Return | Plan the return | A final consultation. You leave with a practice for home and a way to reach your practitioner afterwards. | Follow-up booked',
                     week)
WHERE slug = 'arogya';
