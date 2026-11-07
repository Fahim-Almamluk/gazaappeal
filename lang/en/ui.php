<?php

return [
    'brand' => [
        'name' => 'Gaza Appeal',
        'owner' => 'Gaza Electricity Distribution Company',
    ],

    'common' => [
        'back' => 'Back',
        'close' => 'Close',
        'menu' => 'Menu',
        'skip_to_content' => 'Skip to content',
        'back_to_top' => 'Back to top',
        'none' => '—',
        'no' => 'No',
        'view_site' => 'View site',
    ],

    'language' => [
        'label' => 'Language',
        'switch' => 'العربية',
        'switch_short' => 'ع',
        'names' => [
            'ar' => 'Arabic',
            'en' => 'English',
        ],
    ],

    'money' => '$:amount',

    'theme' => [
        'label' => 'Appearance',
        'to_dark' => 'Switch to dark mode',
        'to_light' => 'Switch to light mode',
    ],

    'header' => [
        'toggle_sidebar' => 'Collapse sidebar',
        'notifications' => 'Notifications',
        'no_notifications' => 'No new notifications.',
        'new_volunteers' => '{1} 1 new volunteer application|[2,*] :count new volunteer applications',
        'new_messages' => '{1} 1 unread message|[2,*] :count unread messages',
        'account' => 'Account',
        'profile' => 'Profile',
        'logout' => 'Sign out',
    ],

    'actions' => [
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'save' => 'Save',
        'save_changes' => 'Save changes',
        'cancel' => 'Cancel',
        'actions' => 'Actions',
    ],

    'confirm' => [
        'cancel' => 'Cancel',
        'delete' => ['title' => 'Confirm deletion', 'text' => '“:name” will be deleted and removed from the site and lists.', 'ok' => 'Yes, delete'],
        'logout' => ['title' => 'Sign out', 'text' => 'Your current session will end and you will need to sign in again.', 'ok' => 'Sign out'],
        'archive' => ['title' => 'Archive message', 'text' => 'The message from “:name” will move to the archive and can be restored later.', 'ok' => 'Yes, archive'],
        'restore' => ['title' => 'Restore message', 'text' => 'The message from “:name” will return to the inbox.', 'ok' => 'Yes, restore'],
        'deactivate' => ['title' => 'Deactivate account', 'text' => '“:name” will be blocked from the dashboard immediately.', 'ok' => 'Yes, deactivate'],
        'activate' => ['title' => 'Activate account', 'text' => '“:name” will be able to access the dashboard again.', 'ok' => 'Yes, activate'],
        'reset_menu' => ['title' => 'Restore the default menu', 'text' => 'Your changes to the site menu will be discarded and the original menu will return.', 'ok' => 'Yes, restore'],
        'reset_home' => ['title' => 'Restore the home layout', 'text' => 'The order and visibility of the sections will return to the original.', 'ok' => 'Yes, restore'],
        'impersonate' => ['title' => 'Sign in as user', 'text' => 'You will browse the dashboard with “:name”’s permissions until you switch back.', 'ok' => 'Yes, sign in'],
    ],

    'sort' => [
        'drag' => 'Drag to reorder',
        'up' => 'Move up',
        'down' => 'Move down',
        'toggle' => 'Show or hide',
    ],

    'flash' => [
        'invalid' => 'Could not save. Check the fields marked in red.',
    ],

    'password' => [
        'min' => 'At least 8 characters',
        'case' => 'An upper and a lower case letter (A and a)',
        'digit' => 'At least one number',
        'weak' => 'Weak',
        'medium' => 'Medium',
        'strong' => 'Strong',
        'very_strong' => 'Very strong',
    ],

    'results' => [
        'total' => 'Results',
        'range' => 'Showing :from – :to',
    ],

    'filter' => [
        'apply' => 'Search',
        'reset' => 'Clear',
    ],

    'form' => [
        'required_hint' => 'Fields marked with an asterisk are required.',
        'publishing' => 'Publishing & order',
        'publishing_hint' => 'Control whether the record appears on the site and where.',
        'is_published' => 'Published on the site',
        'is_published_hint' => 'When off, it is hidden from the site but stays in the dashboard.',
        'sort_order' => 'Order',
        'sort_order_hint' => 'Lower numbers appear first.',
        'translations' => 'Content language',
        'missing_translation' => 'No English translation yet; Arabic will be shown.',
    ],

    'status' => [
        'published' => 'Published',
        'hidden' => 'Hidden',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'upload' => [
        'hint' => 'JPG, PNG or WebP, up to :size MB.',
        'empty' => 'No image',
        'choose' => 'Choose image',
        'replace' => 'Replace image',
    ],

    'nav' => [
        'main' => 'Main',
        'dashboard' => 'Dashboard',
        'content' => 'Content',
        'projects' => 'Projects',
        'losses' => 'Losses',
        'damages' => 'Governorate damage',
        'stories' => 'Success stories',
        'gallery' => 'Photo gallery',
        'partners' => 'Partners',
        'faqs' => 'FAQ',
        'pages' => 'Page content',
        'toggle_group' => 'Expand or collapse :name',
        'giving' => 'Giving',
        'donation_methods' => 'Donation methods',
        'inbox' => 'Inbox',
        'volunteers' => 'Volunteer applications',
        'messages' => 'Contact messages',
        'appearance' => 'Site appearance',
        'menu' => 'Site menu',
        'home_sections' => 'Home sections',
        'system' => 'System',
        'users' => 'Users',
        'roles' => 'Roles & permissions',
        'lookups' => 'Constants',
        'settings' => 'Site settings',
    ],

    'footer' => [
        'rights' => 'All rights reserved © :year Gaza Electricity Distribution Company.',
        'version' => 'Version :version',
    ],
];
