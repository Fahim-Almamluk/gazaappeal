<?php

use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Loss;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;

return [
    'projects' => ['model' => Project::class, 'module' => 'projects', 'publishable' => true],
    'losses' => ['model' => Loss::class, 'module' => 'losses', 'publishable' => true],
    'damages' => ['model' => Damage::class, 'module' => 'damages', 'publishable' => false],
    'stories' => ['model' => Story::class, 'module' => 'stories', 'publishable' => true],
    'gallery' => ['model' => GalleryItem::class, 'module' => 'gallery', 'publishable' => true],
    'partners' => ['model' => Partner::class, 'module' => 'partners', 'publishable' => true],
    'faqs' => ['model' => Faq::class, 'module' => 'faqs', 'publishable' => true],
    'donation-methods' => ['model' => DonationMethod::class, 'module' => 'donation_methods', 'publishable' => true],
];
