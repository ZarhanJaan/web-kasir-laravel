<?php

namespace App\Exports;

use App\Models\PenjualanModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class PenjualanExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PenjualanModel::all();
    }
}
