<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Properties;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class SpreadsheetExport
{
    private const int CHUNK = 500;

    private const float COLUMN_WIDTH = 26;

    public function __construct(private readonly Localization $localization) {}

    /**
     * @param  list<string>  $headings
     * @param  Closure(mixed): list<scalar|null>  $row
     */
    public function download(string $name, string $sheet, array $headings, Builder $query, Closure $row): StreamedResponse
    {
        $filename = $name.'-'.now()->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($sheet, $headings, $query, $row): void {
            $app = config('app.name');
            $writer = new Writer(new Options(
                DEFAULT_COLUMN_WIDTH: self::COLUMN_WIDTH,
                properties: new Properties(title: $sheet, application: $app, creator: $app, lastModifiedBy: $app),
            ));
            $writer->openToFile('php://output');

            $worksheet = $writer->getCurrentSheet();
            $worksheet->setName($sheet);
            $worksheet->setSheetView((new SheetView)->withRightToLeft($this->localization->direction() === 'rtl'));

            $writer->addRow(Row::fromValuesWithStyle($headings, $this->headingStyle()));

            $query->chunk(self::CHUNK, function ($records) use ($writer, $row): void {
                foreach ($records as $record) {
                    $writer->addRow(Row::fromValues($row($record)));
                }
            });

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function yesNo(bool $value): string
    {
        return __($value ? 'admin.export.yes' : 'admin.export.no');
    }

    private function headingStyle(): Style
    {
        return new Style(
            fontBold: true,
            fontColor: Color::WHITE,
            cellAlignment: CellAlignment::CENTER,
            backgroundColor: '1568C9',
        );
    }
}
