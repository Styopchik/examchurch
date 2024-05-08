<?php

namespace App\Exports;

use App\Models\FormValue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FormValuesExport implements FromView
{
    public $request;
    public $startDate;
    public $endDate;

    public function __construct($request, $startDate, $endDate)
    {
        $this->request      = $request;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $formValues = FormValue::where('form_id', $this->request->form_id);
        if ($this->startDate && $this->endDate) {
            $formValues->whereBetween('form_values.created_at', [$this->startDate, $this->endDate]);
        }
        $formValues = $formValues->get();
        return view('export.form-value', [
            'formValues' => $formValues
        ]);
    }
}
