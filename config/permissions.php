<?php

$crud = ['view', 'create', 'update', 'delete'];

return [
    'tree' => [
        'content' => [
            'icon' => 'file-text',
            'modules' => [
                'projects' => ['icon' => 'solar-panel', 'actions' => $crud],
                'losses' => ['icon' => 'zap-off', 'actions' => $crud],
                'damages' => ['icon' => 'map-pin', 'actions' => $crud],
                'stories' => ['icon' => 'award', 'actions' => $crud],
                'gallery' => ['icon' => 'images', 'actions' => $crud],
                'partners' => ['icon' => 'handshake', 'actions' => $crud],
                'faqs' => ['icon' => 'circle-help', 'actions' => $crud],
                'pages' => ['icon' => 'file-text', 'actions' => ['update']],
            ],
        ],
        'giving' => [
            'icon' => 'hand-coins',
            'modules' => [
                'donation_methods' => ['icon' => 'hand-coins', 'actions' => $crud],
            ],
        ],
        'inbox' => [
            'icon' => 'inbox',
            'modules' => [
                'volunteers' => ['icon' => 'user-plus', 'actions' => ['view', 'update']],
                'messages' => ['icon' => 'mail', 'actions' => ['view', 'update']],
            ],
        ],
        'system' => [
            'icon' => 'settings',
            'modules' => [
                'users' => ['icon' => 'users', 'actions' => ['view', 'create', 'update']],
                'roles' => ['icon' => 'shield-check', 'actions' => $crud],
                'lookups' => ['icon' => 'tags', 'actions' => $crud],
                'settings' => ['icon' => 'settings', 'actions' => ['update']],
            ],
        ],
    ],

    'route_modules' => [
        'donation-methods' => 'donation_methods',
    ],

    'route_actions' => [
        'index' => 'view',
        'show' => 'view',
        'export' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'update',
        'update' => 'update',
        'toggle' => 'update',
        'archive' => 'update',
        'restore' => 'update',
        'permissions' => 'update',
        'impersonate' => 'update',
        'destroy' => 'delete',
    ],

    'public_routes' => ['admin.dashboard', 'admin.profile.edit', 'admin.profile.update', 'admin.profile.password', 'admin.impersonation.leave'],
];
