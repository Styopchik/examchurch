<?php

namespace App\DataTables;

use App\Facades\UtilityFacades;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TestimonialsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($request) {
                return UtilityFacades::dateTimeFormat($request->created_at);
            })
            ->editColumn('rating', function (Testimonial $row) {
                $starrating = '<div class="text-center">';
                for ($i = 1; $i <= 5; $i++) {
                    if ($row->rating < $i) {
                        if (is_float($row->rating) && (round($row->rating) == $i)) {
                            $starrating .= '<i class="text-warning fas fa-star-half-alt"></i>';
                        } else {
                            $starrating .= '<i class="fas fa-star"></i>';
                        }
                    } else {
                        $starrating .= '<i class="text-warning fas fa-star"></i>';
                    }
                }
                $starrating .= '<br><span class="theme-text-color">(' . number_format($row->rating, 1) . ')</span></div>';
                return $starrating;
            })->editColumn('image', function (Testimonial $row) {
                if ($row->image) {
                    if (Storage::url($row->image)) {
                        $image = '<img src="' . Storage::url($row->image) . '" style="width:60px; ">';
                    } else {
                        $image = '<img src="' . Storage::url('not-exists-data-images/350x250.png') . '" style="width:60px;  ">';
                    }
                } else {
                    $image = '<img src="' . Storage::url('not-exists-data-images/350x250.png') . '" style="width:60px; ">';
                }
                return $image;
            })->editColumn('status', function (Testimonial $row) {
                if ($row->status == 1) {
                    $status = '<div class="form-check form-switch">
                                            <input class="form-check-input chnageStatus" checked type="checkbox" role="switch" id="' . $row->id . '" data-url="' . route('testimonials.status', $row->id) . '">
                                        </div>';
                } else {
                    $status =  '<div class="form-check form-switch">
                                            <input class="form-check-input chnageStatus" type="checkbox" role="switch" id="' . $row->id . '" data-url="' . route('testimonials.status', $row->id) . '">
                                        </div>';
                }
                return $status;
            })
            ->addColumn('action', function (Testimonial $row) {
                return view('testimonials.action', compact('row'));
            })
            ->rawColumns(['created_at', 'action', 'rating', 'image', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Testimonial $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Testimonial $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        $pdfButton     = [];
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            $pdfButton = ["extend" => "pdf", "text" => '<i class="fas fa-file-pdf"></i>' . __('PDF'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]];
        }
        return $this->builder()
            ->setTableId('testimonials-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "paginate" => [
                    "next" => '<i class="ti ti-chevron-right"></i>',
                    "previous" => '<i class="ti ti-chevron-left"></i>'
                ],
                'lengthMenu' => __("_MENU_") . __('Entries Per Page'),
                "searchPlaceholder" => __('Search...'), "search" => "",
                "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')
            ])
            ->initComplete('function() {
                        var table = this;
                        var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                        searchInput.removeClass(\'form-control form-control-sm\');
                        searchInput.addClass(\'dataTable-input\');
                        var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm \').addClass(\'dataTable-selector\');
                    }')
            ->parameters([
                "dom" =>  "
                <'dataTable-top row'<'dataTable-dropdown page-dropdown col-lg-2 col-sm-12'l><'dataTable-botton table-btn col-lg-6 col-sm-12'B><'dataTable-search tb-search col-lg-3 col-sm-12'f>>
                <'dataTable-container dropdown-icon'<'col-sm-12'tr>>
                <'dataTable-bottom row'<'col-sm-5'i><'col-sm-7'p>>
                ",
                'buttons'   => [
                    ['extend' => 'create', 'className' => 'btn btn-light-primary no-corner me-1 add_module', 'action' => " function ( e, dt, node, config ) {
                                window.location = '" . route('testimonials.create') . "';
                           }"],
                           [
                            'extend' => 'collection', 'className' => 'btn btn-light-secondary me-1 dropdown-toggle', 'text' => '<i class="ti ti-download"></i> ' . __('Export'), "buttons" => [
                                ["extend" => "print", "text" => '<i class="fas fa-print"></i> ' . __('Print'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]],
                                ["extend" => "csv", "text" => '<i class="fas fa-file-csv"></i> ' . __('CSV'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]],
                                ["extend" => "excel", "text" => '<i class="fas fa-file-excel"></i> ' . __('Excel'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]],
                                $pdfButton
                            ],
                        ],
                    ['extend' => 'reset', 'className' => 'btn btn-light-danger me-1'],
                    ['extend' => 'reload', 'className' => 'btn btn-light-warning'],
                ],
                "drawCallback" => 'function( settings ) {
                            var tooltipTriggerList = [].slice.call(
                                document.querySelectorAll("[data-bs-toggle=tooltip]")
                              );
                              var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                              });
                              var popoverTriggerList = [].slice.call(
                                document.querySelectorAll("[data-bs-toggle=popover]")
                              );
                              var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                                return new bootstrap.Popover(popoverTriggerEl);
                              });
                              var toastElList = [].slice.call(document.querySelectorAll(".toast"));
                              var toastList = toastElList.map(function (toastEl) {
                                return new bootstrap.Toast(toastEl);
                              });
                        }'
            ])->language([
                'buttons' => [
                    'create' => __('Create'),
                    'export' => __('Export'),
                    'print' => __('Print'),
                    'reset' => __('Reset'),
                    'reload' => __('Reload'),
                    'excel' => __('Excel'),
                    'csv' => __('CSV'),
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('No')->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('title')->title(__('Title')),
            Column::make('image')->title(__('Image')),
            Column::make('status')->title(__('Status')),
            Column::make('rating')->title(__('Star Rating'))->addClass('text-center'),
            Column::make('created_at')->title(__('Created AT')),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-end')
                ->width('20%'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Testimonials_' . date('YmdHis');
    }
}
