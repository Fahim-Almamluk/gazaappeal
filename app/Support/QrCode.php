<?php

declare(strict_types=1);

namespace App\Support;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\HtmlString;

final class QrCode
{
    public static function svg(string $data, int $size = 200): HtmlString
    {
        $style = new RendererStyle(
            $size,
            1,
            new RoundnessModule(RoundnessModule::SOFT),
            null,
            Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(11, 16, 48)),
        );

        $svg = (new Writer(new ImageRenderer($style, new SvgImageBackEnd)))->writeString($data, 'UTF-8');

        return new HtmlString((string) preg_replace('/^<\?xml[^>]*>\s*/', '', $svg));
    }
}
