<?php

namespace App\DataTables;

use App\Facades\UtilityFacades;
use App\Models\RequestUser;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RequestuserDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($requestuser) {
                return UtilityFacades::dateTimeFormat($requestuser->created_at);
            })
            ->addColumn('action', function (RequestUser $requestuser) {
                return view('requestuser.action', compact('requestuser'));
            })
            ->editColumn('status', function (RequestUser $requestuser) {
                if ($requestuser->is_approved == 1) {
                    return '<span class="p-2 px-3 badge rounded-pill bg-success">' . __('Active') . '</span>';
                } elseif ($requestuser->is_approved == 2) {
                    return '<span class="p-2 px-3 badge rounded-pill bg-danger">' . __('Deactive') . '</span>';
                } else {
                    return '<span class="p-2 px-3 badge rounded-pill bg-warning">' . __('pending') . '</span>';
                }
            })
            ->editColumn('payment_status', function (RequestUser $requestuser) {
                if($requestuser->payStatus->status == 0 && $requestuser->payStatus->plan_id  == 1){
                    return '<span class="p-2 px-3 badge rounded-pill bg-primary">'.__('Free').'</span>';
                }elseif($requestuser->payStatus->status == 2 &&  $requestuser->payStatus->plan_id  > 1){
                    return '<span class="p-2 px-3 badge rounded-pill bg-danger">'.__('Cancel').'</span>';
                }elseif($requestuser->payStatus->status == 1 &&  $requestuser->payStatus->plan_id  > 1){
                    return '<span class="p-2 px-3 badge rounded-pill bg-success">'.__('Success').'</span>';
                }elseif($requestuser->payStatus->status == 3 &&  $requestuser->payStatus->plan_id  > 1){
                    return '<span class="p-2 px-3 badge rounded-pill bg-info">'.__('Offline').'</span>';
                }else{
                    return '<span class="p-2 px-3 badge rounded-pill bg-warning">'.__('pending').'</span>';
                }
            })
            ->rawColumns(['action','status','payment_status']);
    }

    public function query(RequestUser $model)
    {
        return $model->newQuery()->select(['request_users.*','plans.name as plan_name'])
        ->join('orders','orders.request_user_id','=','request_users.id')
        ->join('plans','orders.plan_id','=','plans.id');
    }

    public function html()
    {
        $pdfButton     = [];
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            $pdfButton = ["extend" => "pdf", "text" => '<i class="fas fa-file-pdf"></i>' . __('PDF'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]];
        }
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "paginate" => [
                    "next" => '<i class="ti ti-chevron-right"></i>',
                    "previous" => '<i class="ti ti-chevron-left"></i>'
                ],
                'lengthMenu' => __("_MENU_").__('Entries Per Page'),
                "searchPlaceholder" => __('Search...'), "search" => "",
                "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')
            ])
            ->initComplete('function() {
                var table = this;
                var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                searchInput.removeClass(\'form-control form-control-sm\');
                searchInput.addClass(\'dataTable-input\');
                var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
            }')
            ->parameters([
                "dom" =>  "
                <'dataTable-top row'<'dataTable-dropdown page-dropdown col-lg-2 col-sm-12'l><'dataTable-botton table-btn col-lg-6 col-sm-12'B><'dataTable-search tb-search col-lg-3 col-sm-12'f>>
                <'dataTable-container dropdown-icon'<'col-sm-12'tr>>
                <'dataTable-bottom row'<'col-sm-5'i><'col-sm-7'p>>
                               ",
                'buttons'   => [
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

    protected function getColumns()
    {
        return [
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('email')->title(__('Email')),
            Column::make('plan_name')->name('plans.name')->title(__('Plan Name')),
            Column::make('status')->title(__('Status'))->orderable(false)->searchable(false),
            Column::make('payment_status')->title(__('Payment Status'))->orderable(false)->searchable(false),
            Column::computed('action')->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-end')
                ->width('20%'),
        ];
    }

    protected function filename(): string
    {
        return 'Requestuser_' . date('YmdHis');
    }
}