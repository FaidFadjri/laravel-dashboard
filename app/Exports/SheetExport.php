<?php

namespace App\Exports;

use App\Models\ChecksheetModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SheetExport implements FromCollection, WithHeadings, ShouldAutoSize, WithProperties
{
    protected $data;
    protected $header;

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct($data, $header)
    {
        $this->data     = $data;
        $this->header = $header;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return $this->header;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Patrick Brouwers',
            'lastModifiedBy' => 'Patrick Brouwers',
            'title'          => 'Invoices Export',
            'description'    => 'Latest Invoices',
            'subject'        => 'Invoices',
            'keywords'       => 'invoices,export,spreadsheet',
            'category'       => 'Invoices',
            'manager'        => 'Patrick Brouwers',
            'company'        => 'Maatwebsite',
        ];
    }
}
