<?php
declare(strict_types=1);

defined('ADMIN') || exit;

/**
 * Content types managed in the admin. Each entry maps to a database table and
 * drives the list + edit screens in admin/manage.php.
 *
 * Field keys = column names. Options:
 *   type      text | rich | textarea | slug | image | int | decimal | bool | select | datetime | email | readonly
 *   required  must not be empty
 *   max       maximum characters (matches the column size)
 *   options   for select: value => label
 *   from      for slug: the field used to generate it
 *   default   value for new records
 *
 * Entity options:
 *   list        columns shown in the list (first image column shows a thumbnail)
 *   order       SQL ORDER BY for the list
 *   search      columns searched by the list search box
 *   can_create  false for records that only arrive from the website (enquiries, subscribers)
 *   export      offer a CSV download
 */
function admin_entities(): array
{
    static $entities = null;
    if ($entities !== null) {
        return $entities;
    }

    $sort = ['label' => 'Display order', 'type' => 'int', 'default' => 0, 'help' => 'Lower numbers show first.'];

    $entities = [
        'hero_slides' => [
            'label' => 'Hero slides', 'singular' => 'hero slide', 'icon' => 'slides',
            'intro' => 'The large banner at the top of the homepage. Add a second active slide and the slider arrows appear automatically.',
            'order' => 'sort_order, id', 'title' => 'eyebrow',
            'list'  => ['image' => 'Image', 'eyebrow' => 'Small heading', 'sort_order' => 'Order', 'is_active' => 'Shown'],
            'fields' => [
                'eyebrow'    => ['label' => 'Small heading (above the title)', 'type' => 'text', 'required' => true, 'max' => 160],
                'title_html' => ['label' => 'Title', 'type' => 'rich', 'required' => true, 'max' => 400],
                'subtitle'   => ['label' => 'Text under the title', 'type' => 'textarea', 'required' => true, 'max' => 400, 'rows' => 3],
                'image'      => ['label' => 'Background image', 'type' => 'image', 'required' => true, 'max' => 255],
                'cta_label'  => ['label' => 'Button label', 'type' => 'text', 'required' => true, 'max' => 60, 'default' => 'Explore Journeys'],
                'cta_url'    => ['label' => 'Button link', 'type' => 'text', 'required' => true, 'max' => 255, 'default' => 'journeys', 'help' => 'A page on this site such as journeys or enquire, or a full https:// address.'],
                'sort_order' => $sort,
                'is_active'  => ['label' => 'Show on the website', 'type' => 'bool', 'default' => 1],
            ],
        ],
        'retreats' => [
            'label' => 'Wellness retreats', 'singular' => 'retreat', 'icon' => 'leaf',
            'intro' => 'Cards in the “Wellness Holidays” section of the homepage (up to 8 featured retreats).',
            'order' => 'sort_order, id', 'title' => 'title', 'search' => ['title', 'summary'],
            'list'  => ['image' => 'Image', 'title' => 'Title', 'duration' => 'Duration', 'sort_order' => 'Order', 'is_featured' => 'On homepage'],
            'fields' => [
                'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 160],
                'slug'        => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 140, 'from' => 'title'],
                'duration'    => ['label' => 'Duration', 'type' => 'text', 'required' => true, 'max' => 60, 'default' => '5 Days / 4 Nights'],
                'summary'     => ['label' => 'Short summary (card text)', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'description' => ['label' => 'Full description (for the retreat page)', 'type' => 'paras', 'rows' => 10],
                'image'       => ['label' => 'Image (portrait)', 'type' => 'image', 'required' => true, 'max' => 255],
                'is_featured' => ['label' => 'Show on the homepage', 'type' => 'bool', 'default' => 1],
                'sort_order'  => $sort,
            ],
        ],
        'journeys' => [
            'label' => 'Journeys', 'singular' => 'journey', 'icon' => 'map',
            'intro' => 'Cards in the “Mārgha Journeys” section of the homepage (up to 6 featured), filtered by path.',
            'order' => 'sort_order, id', 'title' => 'title', 'search' => ['title', 'summary', 'region'],
            'list'  => ['image' => 'Image', 'title' => 'Title', 'path' => 'Path', 'price_from' => 'From $/night', 'sort_order' => 'Order', 'is_featured' => 'On homepage'],
            'fields' => [
                'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 160],
                'slug'        => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 140, 'from' => 'title'],
                'path'        => ['label' => 'Path', 'type' => 'select', 'required' => true, 'options' => get_journey_paths()],
                'region'      => ['label' => 'Region', 'type' => 'text', 'max' => 80],
                'price_from'  => ['label' => 'Price from (USD per night)', 'type' => 'decimal', 'required' => true, 'default' => 0],
                'summary'     => ['label' => 'Short summary (card text)', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'description' => ['label' => 'Full description (for the journey page)', 'type' => 'paras', 'rows' => 10],
                'image'       => ['label' => 'Image (square)', 'type' => 'image', 'required' => true, 'max' => 255],
                'is_featured' => ['label' => 'Show on the homepage', 'type' => 'bool', 'default' => 1],
                'sort_order'  => $sort,
            ],
        ],
        'posts' => [
            'label' => 'Journal & news', 'singular' => 'post', 'icon' => 'book',
            'intro' => 'Published posts appear in “From the journal” on the homepage, newest first (up to 9).',
            'order' => 'COALESCE(published_at, created_at) DESC, id DESC', 'title' => 'title', 'search' => ['title', 'category', 'excerpt'],
            'list'  => ['image' => 'Image', 'title' => 'Title', 'type' => 'Type', 'category' => 'Category', 'status' => 'Status', 'published_at' => 'Published'],
            'fields' => [
                'title'        => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 200],
                'slug'         => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 160, 'from' => 'title'],
                'type'         => ['label' => 'Type', 'type' => 'select', 'required' => true, 'options' => ['journal' => 'Journal', 'news' => 'News'], 'default' => 'journal'],
                'category'     => ['label' => 'Category', 'type' => 'text', 'required' => true, 'max' => 80, 'default' => 'Ayurveda & Education'],
                'excerpt'      => ['label' => 'Excerpt (card text)', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'body'         => ['label' => 'Article', 'type' => 'paras', 'rows' => 16],
                'image'        => ['label' => 'Image (portrait)', 'type' => 'image', 'required' => true, 'max' => 255],
                'status'       => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['draft' => 'Draft — hidden', 'published' => 'Published — visible'], 'default' => 'draft'],
                'published_at' => ['label' => 'Publish date', 'type' => 'datetime', 'help' => 'Leave empty to use the moment it is first published.'],
            ],
        ],
        'enquiries' => [
            'label' => 'Enquiries', 'singular' => 'enquiry', 'icon' => 'mail', 'can_create' => false, 'export' => true,
            'intro' => 'Enquiries sent from the website. Update the status as you follow up.',
            'order' => 'created_at DESC, id DESC', 'title' => 'full_name', 'search' => ['full_name', 'email', 'country', 'message'],
            'list'  => ['created_at' => 'Received', 'full_name' => 'Name', 'email' => 'Email', 'interest' => 'Interest', 'status' => 'Status'],
            'fields' => [
                'status'       => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed']],
                'full_name'    => ['label' => 'Name', 'type' => 'readonly'],
                'email'        => ['label' => 'Email', 'type' => 'readonly'],
                'phone'        => ['label' => 'Phone', 'type' => 'readonly'],
                'country'      => ['label' => 'Country', 'type' => 'readonly'],
                'travel_month' => ['label' => 'Travel month', 'type' => 'readonly'],
                'nights'       => ['label' => 'Nights', 'type' => 'readonly'],
                'budget'       => ['label' => 'Budget', 'type' => 'readonly'],
                'interest'     => ['label' => 'Interest', 'type' => 'readonly'],
                'message'      => ['label' => 'Message', 'type' => 'readonly'],
                'created_at'   => ['label' => 'Received', 'type' => 'readonly'],
            ],
        ],
        'subscribers' => [
            'label' => 'Subscribers', 'singular' => 'subscriber', 'icon' => 'users', 'can_create' => false, 'export' => true,
            'intro' => 'Newsletter sign-ups from the website footer.',
            'order' => 'created_at DESC, id DESC', 'title' => 'email', 'search' => ['email'],
            'list'  => ['email' => 'Email', 'status' => 'Status', 'source' => 'Source', 'created_at' => 'Signed up'],
            'fields' => [
                'email'      => ['label' => 'Email', 'type' => 'readonly'],
                'status'     => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed']],
                'source'     => ['label' => 'Source', 'type' => 'readonly'],
                'created_at' => ['label' => 'Signed up', 'type' => 'readonly'],
            ],
        ],
    ];
    return $entities;
}

function admin_entity(string $key): ?array
{
    $all = admin_entities();
    return isset($all[$key]) ? $all[$key] + ['key' => $key, 'table' => $key] : null;
}
