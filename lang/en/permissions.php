<?php

return [
    'groups' => [
        'content' => 'Content',
        'giving' => 'Giving',
        'inbox' => 'Inbox',
        'system' => 'System',
    ],

    'modules' => [
        'projects' => 'Projects',
        'losses' => 'Losses',
        'damages' => 'Governorate damage',
        'stories' => 'Success stories',
        'gallery' => 'Photo gallery',
        'partners' => 'Partners',
        'faqs' => 'FAQ',
        'pages' => 'Page content',
        'donation_methods' => 'Donation methods',
        'volunteers' => 'Volunteer applications',
        'messages' => 'Contact messages',
        'users' => 'Users',
        'roles' => 'Roles & permissions',
        'lookups' => 'Constants',
        'settings' => 'Site settings',
    ],

    'actions' => [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Edit',
        'delete' => 'Delete',
    ],

    'overrides' => [
        'volunteers' => ['update' => 'Follow up'],
        'messages' => ['update' => 'Archive & restore'],
        'pages' => ['update' => 'Edit page texts'],
        'users' => ['update' => 'Edit, activate & permissions'],
        'settings' => ['update' => 'Manage settings'],
    ],

    'tree' => [
        'search' => 'Search for a module or permission…',
        'select_all' => 'Select all',
        'clear_all' => 'Clear all',
        'all' => 'All',
        'toggle' => 'Expand or collapse :name',
    ],

    'legend' => [
        'active' => 'Enabled',
        'inactive' => 'Disabled',
        'inherited' => 'From role',
        'granted' => 'Granted to this user',
        'revoked' => 'Revoked from this user',
    ],
];
