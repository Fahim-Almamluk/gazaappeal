<?php

namespace Database\Seeders;

use App\Enums\DonationMethodType;
use App\Enums\Governorate;
use App\Enums\LookupType;
use App\Enums\LossIcon;
use App\Enums\VolunteerStatus;
use App\Models\ContactMessage;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\GalleryItem;
use App\Models\Loss;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;
use App\Models\Volunteer;
use Database\Seeders\Concerns\SeedsLookups;
use Database\Seeders\Concerns\SeedsTranslations;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

final class DemoContentSeeder extends Seeder
{
    use SeedsLookups;
    use SeedsTranslations;

    public function run(): void
    {
        $this->call(ContentSeeder::class);

        $this->seedProjects();
        $this->seedStories();
        $this->seedGallery();
        $this->seedPartners();
        $this->seedBankAccounts();
        $this->seedLosses();
        $this->seedDamages();
        $this->seedInbox();
    }

    private function seedProjects(): void
    {
        $projects = [
            'حلول الطاقة لتنقية مياه الشرب' => ['project-water', 180000, 112500, 'قيد التنفيذ',
                'تعتمد محطات تحلية المياه الصغيرة في غزة على الكهرباء لتشغيل المضخات وأغشية التنقية، ومع انقطاع الشبكة توقف معظمها عن العمل وأصبحت آلاف العائلات بلا مياه شرب آمنة.',
                'Gaza’s small desalination plants rely on electricity to run their pumps and purification membranes. With the grid down, most of them have stopped, leaving thousands of families without safe drinking water.'],
            'محطات الطاقة للرعاية الطبية' => ['project-clinic', 240000, 96000, 'قيد التنفيذ',
                'تحتاج النقاط الطبية في مخيمات النزوح إلى كهرباء مستقرة لتشغيل أجهزة الفحص وحفظ اللقاحات والأدوية في درجات حرارة آمنة.',
                'Medical points in displacement camps need a stable power supply to run diagnostic equipment and keep vaccines and medicines at safe temperatures.'],
            'محطات الطاقة لمخيمات الإيواء' => ['project-shelters', 150000, 121000, 'قيد التنفيذ',
                'تعيش آلاف العائلات النازحة في خيام بلا إنارة ولا وسيلة لشحن الهواتف أو تشغيل الأجهزة الأساسية، ما يزيد من صعوبة الحياة ليلًا ويعرّض الأطفال للخطر.',
                'Thousands of displaced families live in tents with no lighting and no way to charge phones or run basic devices, making nights harder and putting children at risk.'],
            'الطاقة الشمسية لمراكز رعاية الأطفال' => ['project-children', 90000, 38500, 'قيد التخطيط',
                'تستقبل مراكز رعاية الأطفال يوميًا مئات الأطفال لتقديم الدعم النفسي والتعليم، وتحتاج إلى إنارة وتهوية وطاقة لتشغيل أنشطتها بأمان.',
                'Children’s care centres welcome hundreds of children every day for psychosocial support and learning, and need lighting, ventilation and power to run their activities safely.'],
            'الطاقة الشمسية لآبار المياه الصغيرة' => ['project-wells', 120000, 120000, 'مكتمل',
                'تغذي الآبار الصغيرة الأحياء السكنية ومناطق الخيام بالمياه، لكن مضخاتها توقفت مع انقطاع الكهرباء ونفاد الوقود.',
                'Small wells supply water to residential neighbourhoods and tent areas, but their pumps stopped when the power was cut and fuel ran out.'],
            'عربات الطاقة الشمسية المتنقلة' => ['project-mobile', 75000, 41000, 'قيد التنفيذ',
                'عربة النور وحدة طاقة شمسية متنقلة تصل إلى أماكن تجمع النازحين لشحن الهواتف والبطاريات وتشغيل الإنارة والأجهزة الطبية البسيطة.',
                'The Light Cart is a mobile solar unit that reaches gathering points of displaced people to charge phones and batteries and to run lighting and basic medical devices.'],
            'حلول الطاقة لمحطات الصرف الصحي' => ['project-sewage', 320000, 64000, 'قيد التخطيط',
                'توقف محطات الصرف الصحي يهدد بانتشار الأمراض وتلوث المياه الجوفية والبحر، وتشغيلها بأنظمة شمسية هجينة يحمي صحة السكان والبيئة.',
                'Idle wastewater stations threaten the spread of disease and the pollution of groundwater and the sea. Running them on hybrid solar systems protects public health and the environment.'],
            'حلول الطاقة لآبار المياه التابعة للبلديات وسلطة المياه' => ['project-municipal', 280000, 147000, 'قيد التنفيذ',
                'تعتمد البلديات على آبار رئيسية لتزويد الأحياء بالمياه عبر الشبكات، وتشغيلها بالطاقة الشمسية يعيد المياه إلى آلاف المنازل.',
                'Municipalities rely on main wells to supply neighbourhoods through the water network. Running them on solar power brings water back to thousands of homes.'],
            'قطع غيار لأنظمة الطاقة المتضررة للأفراد والمؤسسات' => ['project-parts', 60000, 22500, 'قيد التنفيذ',
                'تضررت آلاف أنظمة الطاقة الشمسية المنزلية والمؤسسية، وكثير منها يحتاج فقط إلى قطع غيار بسيطة ليعود إلى العمل.',
                'Thousands of home and institutional solar systems were damaged, and many of them need only simple spare parts to work again.'],
        ];
        $closing = [
            'ar' => [
                'يعمل فريق شركة توزيع كهرباء محافظات غزة على تركيب أنظمة طاقة شمسية مع بطاريات تخزين تضمن استمرار التشغيل ليلًا، مع متابعة فنية دورية للحفاظ على كفاءة الأنظمة.',
                'كل تبرع يقرّبنا من تشغيل هذه المرافق الحيوية، ويعيد جزءًا من الحياة الطبيعية إلى العائلات التي تعتمد عليها يوميًا.',
            ],
            'en' => [
                'The Gaza Electricity Distribution Company team installs solar systems with storage batteries that keep them running at night, with regular technical follow-up to maintain their efficiency.',
                'Every donation brings us closer to running these vital facilities and returns a part of normal life to the families who depend on them every day.',
            ],
        ];

        foreach ($projects as $title => [$image, $goal, $raised, $status, $intro, $introEn]) {
            $project = Project::query()->whereTranslation('title', $title)->first();

            if ($project === null) {
                continue;
            }

            if ($project->image === null) {
                $project->forceFill([
                    'image' => $this->publish($image, 'projects'),
                    'goal_amount' => $goal,
                    'raised_amount' => $raised,
                    'status_id' => $this->lookup(LookupType::ProjectStatus, $status),
                ]);
            }

            $body = $project->translations('body');

            foreach (['ar' => $intro, 'en' => $introEn] as $locale => $opening) {
                $current = $body[$locale] ?? null;

                if ($current === null || $current === $project->translation('summary', $locale)) {
                    $body[$locale] = implode("\n\n", [$opening, ...$closing[$locale]]);
                }
            }

            $project->body = $body;
            $project->save();
        }
    }

    private function seedStories(): void
    {
        $stories = [
            ['story-water', 'حلول الطاقة لتنقية مياه الشرب',
                ['عادت المياه النظيفة إلى مخيم في دير البلح', 'بعد أسابيع من الاعتماد على مياه غير آمنة، أعاد نظام شمسي تشغيل محطة التحلية الصغيرة في المخيم.', 'كانت العائلات تقطع مسافات طويلة يوميًا للحصول على بضعة لترات من المياه. بعد تركيب نظام الطاقة الشمسية عادت المحطة للعمل بطاقتها الكاملة، وأصبحت توفر مياه الشرب لأكثر من ألف عائلة يوميًا.'],
                ['Clean water returns to a camp in Deir al-Balah', 'After weeks of relying on unsafe water, a solar system restarted the camp’s small desalination plant.', 'Families used to walk long distances every day for a few litres of water. Once the solar system was installed, the plant returned to full capacity and now provides drinking water to more than a thousand families every day.']],
            ['story-install', 'الطاقة الشمسية لآبار المياه الصغيرة',
                ['اكتمال تركيب أنظمة الطاقة لآبار المياه', 'أنهت فرقنا الفنية تركيب أنظمة شمسية لعدد من الآبار الصغيرة التي تغذي الأحياء ومناطق الخيام.', 'عمل الفنيون لأيام متواصلة رغم الظروف الصعبة لتركيب الألواح وتوصيل المضخات. اليوم تضخ الآبار المياه مجددًا دون الحاجة إلى وقود، وبتكلفة تشغيل شبه معدومة.'],
                ['Solar systems installed for water wells', 'Our technical teams finished installing solar systems for several small wells that supply neighbourhoods and tent areas.', 'Technicians worked for days on end in difficult conditions to install the panels and connect the pumps. Today the wells pump water again without fuel, at almost no running cost.']],
            ['story-school', 'الطاقة الشمسية لمراكز رعاية الأطفال',
                ['إنارة أول مركز لرعاية الأطفال بالطاقة الشمسية', 'أصبح المركز قادرًا على استقبال الأطفال لساعات أطول بإنارة وتهوية مستمرة.', 'مع وصول الكهرباء إلى المركز عادت الأنشطة التعليمية وجلسات الدعم النفسي إلى جدولها الطبيعي، وأصبح المكان أكثر أمانًا وراحة للأطفال وللفريق العامل.'],
                ['The first children’s care centre lit by solar power', 'The centre can now welcome children for longer hours with continuous lighting and ventilation.', 'With power reaching the centre, learning activities and psychosocial support sessions are back on their normal schedule, and the place is safer and more comfortable for the children and the staff.']],
            ['story-lines', null,
                ['فرق الصيانة الميدانية تعيد تأهيل أنظمة متضررة', 'بقطع غيار بسيطة أعاد فنيونا عشرات الأنظمة الشمسية المنزلية إلى العمل.', 'كثير من الأنظمة المتضررة لم يكن يحتاج سوى إلى محوّل أو بطارية أو كابلات جديدة. بفضل تبرعاتكم وصلت قطع الغيار إلى العائلات والمؤسسات وعاد النور إلى بيوتها.'],
                ['Field maintenance teams restore damaged systems', 'With simple spare parts, our technicians brought dozens of home solar systems back to work.', 'Many damaged systems needed nothing more than a new inverter, battery or cables. Thanks to your donations, spare parts reached families and institutions, and light returned to their homes.']],
        ];

        foreach ($stories as [$image, $projectTitle, [$title, $summary, $body], [$titleEn, $summaryEn, $bodyEn]]) {
            $this->seedTranslated(Story::class, ['title', ['ar' => $title, 'en' => $titleEn]], [
                'project_id' => $projectTitle ? Project::query()->whereTranslation('title', $projectTitle)->value('id') : null,
                'summary' => ['ar' => $summary, 'en' => $summaryEn],
                'body' => ['ar' => $body, 'en' => $bodyEn],
                'image' => $this->publish($image, 'stories'),
                'is_published' => true,
            ]);
        }
    }

    private function seedGallery(): void
    {
        $captions = [
            ['ألواح شمسية جاهزة للتركيب', 'Solar panels ready for installation'],
            ['منظومة طاقة تحت سماء غزة', 'A power system under Gaza’s sky'],
            ['حقل ألواح شمسية لتغذية المرافق', 'A solar field powering vital facilities'],
            ['فنيونا أثناء صيانة الشبكة', 'Our technicians maintaining the grid'],
            ['معدات السلامة في موقع العمل', 'Safety equipment on site'],
            ['ألواح عالية الكفاءة', 'High-efficiency panels'],
            ['المياه تعود إلى الصنابير', 'Water flows from the taps again'],
            ['نور يعود إلى البيوت', 'Light returns to homes'],
        ];

        foreach ($captions as $index => [$caption, $captionEn]) {
            $this->seedTranslated(GalleryItem::class, ['caption', ['ar' => $caption, 'en' => $captionEn]], [
                'image' => $this->publish('gallery-'.($index + 1), 'gallery'),
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedPartners(): void
    {
        $partners = [
            ['مؤسسة نور للتنمية', 'Noor Development Foundation', 'partner-noor'],
            ['صندوق الطاقة الإنسانية', 'Humanitarian Energy Fund', 'partner-energy'],
            ['جمعية المياه النظيفة', 'Clean Water Association', 'partner-water'],
            ['مبادرة شمس غزة', 'Gaza Sun Initiative', 'partner-sun'],
        ];

        foreach ($partners as $index => [$name, $nameEn, $logo]) {
            $partner = $this->seedTranslated(Partner::class, ['name', ['ar' => $name, 'en' => $nameEn]], [
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);

            if ($partner->logo === null) {
                $partner->update(['logo' => $this->publish($logo, 'partners', 'png')]);
            }
        }
    }

    private function seedBankAccounts(): void
    {
        $this->seedTranslated(DonationMethod::class, ['name', ['ar' => 'تحويل بنكي - حساب تجريبي', 'en' => 'Bank Transfer - Demo Account']], [
            'type' => DonationMethodType::BankTransfer,
            'description' => ['ar' => 'حساب تجريبي لمعاينة عرض أكثر من حساب بنكي.', 'en' => 'A demo account to preview several bank accounts.'],
            'bank_details' => [
                'bank_name' => 'بنك تجريبي',
                'bank_name_en' => 'Demo Bank',
                'beneficiary' => 'شركة توزيع كهرباء غزة',
                'beneficiary_en' => 'Gaza Electricity Distribution Company',
                'branch' => 'الرمال',
                'branch_en' => 'Al-Rimal',
                'address' => 'غزة - الرمال',
                'address_en' => 'Al-Rimal, Gaza',
                'account_number' => '100200',
                'swift_code' => 'DEMOPS00',
                'iban' => 'PS00DEMO000000000000001002000',
            ],
            'is_published' => true,
            'sort_order' => 3,
        ]);
    }

    private function seedLosses(): void
    {
        $losses = [
            [['محطات المياه المتوقفة', 'بالمئة', 'نسبة محطات تحلية وضخ المياه التي توقفت عن العمل بسبب انقطاع الكهرباء.'], ['Water stations out of service', 'percent', 'Share of desalination and pumping stations that stopped working because of the power cut.'], 70, LossIcon::Stations],
            [['شبكات الكهرباء المتضررة', 'كم', 'أطوال خطوط الضغط المتوسط والمنخفض التي تعرضت للتدمير الكلي أو الجزئي.'], ['Damaged power networks', 'km', 'Length of medium- and low-voltage lines that were fully or partly destroyed.'], 1200, LossIcon::Cables],
        ];

        foreach ($losses as $order => [[$title, $unit, $description], [$titleEn, $unitEn, $descriptionEn], $figure, $icon]) {
            $this->seedTranslated(Loss::class, ['title', ['ar' => $title, 'en' => $titleEn]], [
                'figure' => $figure,
                'unit' => ['ar' => $unit, 'en' => $unitEn],
                'description' => ['ar' => $description, 'en' => $descriptionEn],
                'icon' => $icon,
                'is_published' => true,
                'sort_order' => $order + 3,
            ]);
        }
    }

    private function seedDamages(): void
    {
        $items = [
            'الشبكات الكهربائية' => [
                ['شبكات الجهد المتوسط', 'Medium-voltage networks', 25_800_000],
                ['شبكات الجهد المنخفض', 'Low-voltage networks', 29_800_000],
                ['محولات التوزيع', 'Distribution transformers', 32_100_000],
                ['عدادات المشتركين', 'Customer meters', 14_400_000],
                ['مبيعات الطاقة', 'Energy sales', 79_400_000],
            ],
            'مبانٍ ومخازن ومراكز' => [
                ['المقرات الإدارية', 'Administrative offices', 6_200_000],
                ['المخازن والمستودعات', 'Stores and warehouses', 9_400_000],
                ['مراكز خدمة الجمهور', 'Customer service centres', 3_100_000],
            ],
            'سيارات وآليات ومولدات' => [
                ['سيارات الصيانة والطوارئ', 'Maintenance and emergency vehicles', 4_300_000],
                ['الرافعات والآليات الثقيلة', 'Cranes and heavy machinery', 7_800_000],
                ['المولدات الاحتياطية', 'Backup generators', 2_600_000],
            ],
            'رواتب ومصاريف تشغيلية' => [
                ['الرواتب والأجور', 'Salaries and wages', 11_500_000],
                ['المصاريف التشغيلية', 'Operating expenses', 5_700_000],
            ],
        ];
        $shares = [
            Governorate::North->value => 0.85,
            Governorate::Gaza->value => 1.0,
            Governorate::Middle->value => 0.45,
            Governorate::KhanYounis->value => 0.75,
            Governorate::Rafah->value => 0.55,
        ];

        foreach ($shares as $governorate => $share) {
            foreach ($items as $category => $categoryItems) {
                foreach ($categoryItems as $order => [$item, $itemEn, $amount]) {
                    $this->seedTranslated(
                        Damage::class,
                        ['item', ['ar' => $item, 'en' => $itemEn]],
                        ['amount' => (int) round($amount * $share, -5), 'sort_order' => $order],
                        ['governorate' => $governorate, 'category_id' => $this->lookup(LookupType::DamageCategory, $category)],
                    );
                }
            }
        }
    }

    private function seedInbox(): void
    {
        $volunteers = [
            ['م. سامر الحلبي', 'samer@example.com', '+970 59 111 2233', 'فلسطين', 'حلول الطاقة المتجددة والهجينة', 12, VolunteerStatus::New],
            ['م. ليلى عودة', 'laila@example.com', '+962 79 444 5566', 'الأردن', 'البنية التحتية للطاقة الكهربائية', 9, VolunteerStatus::Contacted],
            ['د. كريم منصور', 'karim@example.com', '+44 7700 900123', 'المملكة المتحدة', 'إعادة التأهيل والإعمار في قطاع الطاقة', 18, VolunteerStatus::Accepted],
            ['هبة الشامي', 'hiba@example.com', '+90 532 111 2233', 'تركيا', 'إدارة مشاريع الإغاثة والطوارئ', 6, VolunteerStatus::New],
        ];

        foreach ($volunteers as [$name, $email, $phone, $country, $expertise, $years, $status]) {
            $volunteer = Volunteer::query()->firstOrNew(['email' => $email]);
            $volunteer->fill([
                'name' => $name,
                'phone' => $phone,
                'country' => $country,
                'expertise_id' => $this->lookup(LookupType::VolunteerExpertise, $expertise),
                'experience_years' => $years,
                'message' => 'لدي خبرة عملية في هذا المجال وأرغب في المساهمة عن بُعد أو ميدانيًا في تصميم وتنفيذ مشاريع الطاقة في غزة.',
            ]);
            $volunteer->status = $status;
            $volunteer->save();
        }

        $messages = [
            ['أحمد يوسف', 'ahmad@example.com', 'مؤسسة الخير الدولية', 'نرغب في تمويل مشروع كامل لتشغيل آبار المياه، كيف يمكننا التنسيق معكم؟'],
            ['سارة خليل', 'sara@example.com', null, 'هل يمكن التبرع لمشروع محدد عبر التحويل البنكي؟ وكيف أتأكد من وصول التبرع؟'],
            ['Daniel Brooks', 'daniel@example.com', 'Solar Aid Network', 'We would like to donate solar inverters and batteries. Who should we contact?'],
        ];

        foreach ($messages as [$name, $email, $organization, $body]) {
            ContactMessage::query()->firstOrCreate(['email' => $email], [
                'name' => $name,
                'organization' => $organization,
                'body' => $body,
            ]);
        }
    }

    private function publish(string $name, string $folder, string $extension = 'jpg'): string
    {
        $path = "{$folder}/demo-{$name}.{$extension}";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->putFileAs($folder, new File(database_path("data/demo-images/{$name}.{$extension}")), "demo-{$name}.{$extension}");
        }

        return $path;
    }
}
