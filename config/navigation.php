<?php

return [
    'limits' => [
        'roots' => 8,
        'children' => 10,
    ],

    'pages' => [
        'home' => ['label' => 'site.nav.home', 'active' => 'home'],
        'about' => ['label' => 'site.nav.about', 'active' => 'about'],
        'losses' => ['label' => 'site.nav.losses', 'active' => 'losses'],
        'projects.index' => ['label' => 'site.nav.projects', 'active' => 'projects.*'],
        'stories.index' => ['label' => 'site.nav.stories', 'active' => 'stories.*'],
        'gallery' => ['label' => 'site.nav.gallery', 'active' => 'gallery'],
        'donate' => ['label' => 'site.nav.donate', 'active' => 'donate'],
        'volunteer.create' => ['label' => 'site.nav.volunteer', 'active' => 'volunteer.*'],
        'contact.create' => ['label' => 'site.nav.contact', 'active' => 'contact.*'],
        'faq' => ['label' => 'site.nav.faq', 'active' => 'faq'],
        'privacy' => ['label' => 'site.nav.privacy', 'active' => 'privacy'],
    ],

    'defaults' => ['home', 'about', 'losses', 'projects.index', 'volunteer.create', 'stories.index', 'contact.create'],

    'admin' => [
        [
            'key' => 'main',
            'label' => 'ui.nav.main',
            'collapsible' => false,
            'items' => [
                ['route' => 'admin.dashboard', 'label' => 'ui.nav.dashboard', 'icon' => 'layout-dashboard'],
            ],
        ],
        [
            'key' => 'content',
            'label' => 'ui.nav.content',
            'items' => [
                ['route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'permission' => 'projects.view', 'label' => 'ui.nav.projects', 'icon' => 'solar-panel'],
                ['route' => 'admin.losses.index', 'active' => 'admin.losses.*', 'permission' => 'losses.view', 'label' => 'ui.nav.losses', 'icon' => 'zap-off'],
                ['route' => 'admin.damages.index', 'active' => 'admin.damages.*', 'permission' => 'damages.view', 'label' => 'ui.nav.damages', 'icon' => 'map-pin'],
                ['route' => 'admin.stories.index', 'active' => 'admin.stories.*', 'permission' => 'stories.view', 'label' => 'ui.nav.stories', 'icon' => 'award'],
                ['route' => 'admin.gallery.index', 'active' => 'admin.gallery.*', 'permission' => 'gallery.view', 'label' => 'ui.nav.gallery', 'icon' => 'images'],
                ['route' => 'admin.partners.index', 'active' => 'admin.partners.*', 'permission' => 'partners.view', 'label' => 'ui.nav.partners', 'icon' => 'handshake'],
                ['route' => 'admin.faqs.index', 'active' => 'admin.faqs.*', 'permission' => 'faqs.view', 'label' => 'ui.nav.faqs', 'icon' => 'circle-help'],
                ['route' => 'admin.pages.edit', 'active' => 'admin.pages.*', 'permission' => 'pages.update', 'label' => 'ui.nav.pages', 'icon' => 'file-text'],
            ],
        ],
        [
            'key' => 'giving',
            'label' => 'ui.nav.giving',
            'items' => [
                ['route' => 'admin.donation-methods.index', 'active' => 'admin.donation-methods.*', 'permission' => 'donation_methods.view', 'label' => 'ui.nav.donation_methods', 'icon' => 'hand-coins'],
            ],
        ],
        [
            'key' => 'inbox',
            'label' => 'ui.nav.inbox',
            'items' => [
                ['route' => 'admin.volunteers.index', 'active' => 'admin.volunteers.*', 'permission' => 'volunteers.view', 'label' => 'ui.nav.volunteers', 'icon' => 'user-plus'],
                ['route' => 'admin.messages.index', 'active' => 'admin.messages.*', 'permission' => 'messages.view', 'label' => 'ui.nav.messages', 'icon' => 'mail'],
            ],
        ],
        [
            'key' => 'appearance',
            'label' => 'ui.nav.appearance',
            'items' => [
                ['route' => 'admin.menu.index', 'active' => 'admin.menu.*', 'super' => true, 'label' => 'ui.nav.menu', 'icon' => 'list-tree'],
                ['route' => 'admin.home-sections.edit', 'active' => 'admin.home-sections.*', 'super' => true, 'label' => 'ui.nav.home_sections', 'icon' => 'layout-list'],
            ],
        ],
        [
            'key' => 'system',
            'label' => 'ui.nav.system',
            'items' => [
                ['route' => 'admin.users.index', 'active' => 'admin.users.*', 'permission' => 'users.view', 'label' => 'ui.nav.users', 'icon' => 'users'],
                ['route' => 'admin.roles.index', 'active' => 'admin.roles.*', 'permission' => 'roles.view', 'label' => 'ui.nav.roles', 'icon' => 'shield-check'],
                ['route' => 'admin.lookups.index', 'active' => 'admin.lookups.*', 'permission' => 'lookups.view', 'label' => 'ui.nav.lookups', 'icon' => 'tags'],
                ['route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'permission' => 'settings.update', 'label' => 'ui.nav.settings', 'icon' => 'settings'],
            ],
        ],
    ],
];
