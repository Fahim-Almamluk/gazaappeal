<?php

return [
    'groups' => [
        'content' => 'المحتوى',
        'giving' => 'التبرعات',
        'inbox' => 'الوارد',
        'system' => 'النظام',
    ],

    'modules' => [
        'projects' => 'المشاريع',
        'losses' => 'الخسائر',
        'damages' => 'أضرار المحافظات',
        'stories' => 'قصص النجاح',
        'gallery' => 'معرض الصور',
        'partners' => 'الشركاء',
        'faqs' => 'الأسئلة الشائعة',
        'pages' => 'محتوى الصفحات',
        'donation_methods' => 'طرق التبرع',
        'volunteers' => 'طلبات التطوع',
        'messages' => 'رسائل التواصل',
        'users' => 'المستخدمون',
        'roles' => 'الأدوار والصلاحيات',
        'lookups' => 'الثوابت',
        'settings' => 'إعدادات الموقع',
    ],

    'actions' => [
        'view' => 'عرض',
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
    ],

    'overrides' => [
        'volunteers' => ['update' => 'متابعة الطلبات'],
        'messages' => ['update' => 'أرشفة واستعادة'],
        'pages' => ['update' => 'تعديل نصوص الصفحات'],
        'users' => ['update' => 'تعديل وتفعيل وصلاحيات'],
        'settings' => ['update' => 'إدارة الإعدادات'],
    ],

    'tree' => [
        'search' => 'ابحث عن وحدة أو صلاحية…',
        'select_all' => 'تحديد الكل',
        'clear_all' => 'إلغاء الكل',
        'toggle' => 'فتح أو طي قسم :name',
    ],

    'legend' => [
        'inherited' => 'من الدور',
        'granted' => 'مضافة لهذا المستخدم',
        'revoked' => 'محجوبة عن هذا المستخدم',
    ],
];
