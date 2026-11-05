<?php

namespace Database\Seeders;

use App\Enums\DonationMethodType;
use App\Enums\LossIcon;
use App\Enums\ProjectStatus;
use App\Models\DonationMethod;
use App\Models\Faq;
use App\Models\Loss;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsTranslations;
use Illuminate\Database\Seeder;

final class ContentSeeder extends Seeder
{
    use SeedsTranslations;

    public function run(): void
    {
        $this->seedProjects();
        $this->seedLosses();
        $this->seedDonationMethods();
        $this->seedFaqs();
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                ['حلول الطاقة لتنقية مياه الشرب', 'تبرعك يمنح الحياة: ادعم محطات تحلية المياه في غزة بالطاقة الشمسية.'],
                ['Solar Power for Clean Drinking Water', 'Your donation gives life: support Gaza’s desalination plants with solar power.'],
            ],
            [
                ['محطات الطاقة للرعاية الطبية', 'طاقة شمسية لدعم الرعاية الصحية في مخيمات النزوح.'],
                ['Power Stations for Medical Care', 'Solar power to support healthcare in displacement camps.'],
            ],
            [
                ['محطات الطاقة لمخيمات الإيواء', 'دعمكم يضيء حياة العائلات النازحة في غزة.'],
                ['Power Stations for Shelter Camps', 'Your support lights up the lives of displaced families in Gaza.'],
            ],
            [
                ['الطاقة الشمسية لمراكز رعاية الأطفال', 'كل تبرع يضيء أملًا جديدًا: ادعم مراكز رعاية الأطفال في غزة بأنظمة الطاقة الشمسية.'],
                ['Solar Power for Children’s Care Centres', 'Every donation lights new hope: support Gaza’s children’s care centres with solar systems.'],
            ],
            [
                ['الطاقة الشمسية لآبار المياه الصغيرة', 'معًا لإيصال المياه للمنازل والخيام: ادعم مشروع الطاقة الشمسية لآبار المياه.'],
                ['Solar Power for Small Water Wells', 'Together, let’s bring water to homes and tents: support the solar project for water wells.'],
            ],
            [
                ['عربات الطاقة الشمسية المتنقلة', 'عربة النور: دعمكم يضيء حياة العائلات النازحة في غزة.'],
                ['Mobile Solar Power Carts', 'The Light Cart: your support lights up the lives of displaced families in Gaza.'],
            ],
            [
                ['حلول الطاقة لمحطات الصرف الصحي', 'تشغيل محطات الصرف الصحي العامة بأنظمة الطاقة الشمسية الهجينة في غزة: أمل جديد لحياة كريمة.'],
                ['Power Solutions for Wastewater Stations', 'Running Gaza’s public wastewater stations on hybrid solar systems: new hope for a dignified life.'],
            ],
            [
                ['حلول الطاقة لآبار المياه التابعة للبلديات وسلطة المياه', 'في ظل الانقطاع المستمر للكهرباء، تواجه آلاف الأسر في غزة مشكلة نقص المياه.'],
                ['Power Solutions for Municipal and Water Authority Wells', 'With continuous power cuts, thousands of families in Gaza face water shortages.'],
            ],
            [
                ['قطع غيار لأنظمة الطاقة المتضررة للأفراد والمؤسسات', 'ساعدنا في استعادة مصادر الطاقة المحلية لتخطي الكارثة.'],
                ['Spare Parts for Damaged Energy Systems', 'Help individuals and institutions restore their local energy sources to overcome the catastrophe.'],
            ],
        ];

        foreach ($projects as $order => [[$title, $summary], [$titleEn, $summaryEn]]) {
            $this->seedTranslated(Project::class, ['title', ['ar' => $title, 'en' => $titleEn]], [
                'summary' => ['ar' => $summary, 'en' => $summaryEn],
                'body' => ['ar' => $summary, 'en' => $summaryEn],
                'status' => ProjectStatus::Active,
                'is_urgent' => true,
                'is_published' => true,
                'sort_order' => $order + 1,
            ]);
        }
    }

    private function seedLosses(): void
    {
        $losses = [
            [['السكان بلا كهرباء', 'مليون شخص', 'خرجت شبكة الكهرباء عن الخدمة تمامًا وبقي سكان القطاع بلا تيار كهربائي.'], ['Residents without power', 'million people', 'The electricity grid is completely out of service, leaving the Strip’s residents without power.'], 2.5, LossIcon::People],
            [['مدة الانقطاع الكامل', 'شهرًا وأكثر', 'انقطاع شامل للتيار منذ أكتوبر 2023 دون مصدر بديل للشبكة العامة.'], ['Duration of the total blackout', 'months and counting', 'A total power cut since October 2023, with no alternative source for the public grid.'], 12, LossIcon::Power],
        ];

        foreach ($losses as $order => [[$title, $unit, $description], [$titleEn, $unitEn, $descriptionEn], $figure, $icon]) {
            $this->seedTranslated(Loss::class, ['title', ['ar' => $title, 'en' => $titleEn]], [
                'figure' => $figure,
                'unit' => ['ar' => $unit, 'en' => $unitEn],
                'description' => ['ar' => $description, 'en' => $descriptionEn],
                'icon' => $icon,
                'is_published' => true,
                'sort_order' => $order + 1,
            ]);
        }
    }

    private function seedDonationMethods(): void
    {
        $this->seedTranslated(DonationMethod::class, ['name', ['ar' => 'منصة إنساني', 'en' => 'Ensany Platform']], [
            'type' => DonationMethodType::Link,
            'description' => ['ar' => 'تبرّع إلكترونيًا بالبطاقة البنكية عبر منصة إنساني الموثوقة.', 'en' => 'Donate online by bank card through the trusted Ensany platform.'],
            'url' => 'https://gedco.ensany.com/',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $bank = $this->seedTranslated(DonationMethod::class, ['name', ['ar' => 'تحويل بنكي', 'en' => 'Bank Transfer']], [
            'type' => DonationMethodType::BankTransfer,
            'description' => ['ar' => 'حوّل تبرعك مباشرة إلى حساب الشركة في بنك فلسطين.', 'en' => 'Transfer your donation directly to the company’s account at Bank of Palestine.'],
            'bank_details' => [
                'bank_name' => 'بنك فلسطين',
                'beneficiary' => 'شركة توزيع كهرباء غزة',
                'branch' => 'الرمال',
                'address' => 'غزة - الرمال - عمارة النونو',
                'account_number' => '27428',
                'swift_code' => 'PALSPS22',
                'iban' => 'PS05PALS045400274280013000000',
            ],
            'is_published' => true,
            'sort_order' => 2,
        ]);

        $bank->bank_details = [
            'bank_name_en' => 'Bank of Palestine',
            'beneficiary_en' => 'Gaza Electricity Distribution Company',
            'branch_en' => 'Al-Rimal',
            'address_en' => 'Al-Nunu Building, Al-Rimal, Gaza',
            ...($bank->bank_details ?? []),
        ];
        $bank->save();
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                ['كيف يمكنني التبرع؟', 'اختر طريقة التبرع المناسبة لك من صفحة «تبرع الآن»: التبرع الإلكتروني عبر المنصات المعتمدة أو التحويل البنكي المباشر إلى حساب الشركة.'],
                ['How can I donate?', 'Choose the method that suits you on the “Donate Now” page: online giving through our approved platforms, or a direct bank transfer to the company’s account.'],
            ],
            [
                ['كيف أنضم إلى فريق المتطوعين؟', 'املأ نموذج التطوع في صفحة «المتطوعين» وحدد مجال خبرتك، وسيتواصل معك فريقنا بعد مراجعة طلبك.'],
                ['How do I join the volunteer team?', 'Fill in the form on the “Volunteers” page and choose your field of expertise. Our team will contact you after reviewing your application.'],
            ],
            [
                ['كيف أتواصل مع فريق النداء؟', 'يمكنك مراسلتنا من صفحة «تواصل معنا» أو الاتصال على أرقام الشركة الظاهرة أسفل الموقع.'],
                ['How do I contact the appeal team?', 'Message us from the “Contact” page, or call the company numbers shown at the bottom of the site.'],
            ],
        ];

        foreach ($faqs as $order => [[$question, $answer], [$questionEn, $answerEn]]) {
            $this->seedTranslated(Faq::class, ['question', ['ar' => $question, 'en' => $questionEn]], [
                'answer' => ['ar' => $answer, 'en' => $answerEn],
                'is_published' => true,
                'sort_order' => $order + 1,
            ]);
        }
    }
}
