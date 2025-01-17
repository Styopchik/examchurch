<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\Form;
use App\Models\Role;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use App\Facades\UtilityFacades;
use App\Models\FormValue;
use Illuminate\Http\Request;

class FormsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function (Form $form) {
                $st = '';
                if ($form->is_active == 1) {
                    $st = '<div class="form-check form-switch">
                    <input class="form-check-input chnageStatus" checked type="checkbox" role="switch" id="' . $form->id . '" data-url="' . route('form.status', $form->id) . '">
                </div>';
                } else {
                    $st = '<div class="form-check form-switch">
                    <input class="form-check-input chnageStatus" type="checkbox" role="switch" id="' . $form->id . '" data-url="' . route('form.status', $form->id) . '">
                </div>';
                }
                return $st;
            })
            ->editColumn('role', function (Form $form) {
                if ($form->role) {
                    $allrole = '';
                    $roles = explode(",", $form->role);
                    foreach ($roles as $role) {
                        $allrole .=   Role::find($role)->name . ',';
                    }
                    $allroles = rtrim($allrole, ",");
                    return $allroles;
                } else {
                    return;
                }
            })
            ->editColumn('user', function (Form $form) {
                if ($form->user) {
                    $alluser = '';
                    $users = explode(",", $form->user);
                    foreach ($users as $user) {
                        $alluser .=   User::find($user)->name . ',';
                    }
                    $allusers = rtrim($alluser, ",");
                    return $allusers;
                } else {
                    return;
                }
            })
            ->editColumn('created_at', function (Form $form) {
                return UtilityFacades::dateTimeFormat($form->created_at->format('Y-m-d h:i:s'));
            })->editColumn('category_id', function (Form $form) {
                $category = $form->category_name;
                return $category;
            })
            ->addColumn('action', function (Form $form) {
                $hashids = new Hashids();
                $formValue = FormValue::where('form_id', $form->id)->count();
                return view('form.action', compact('form', 'hashids', 'formValue'));
            })
            ->rawColumns(['status', 'location', 'action', 'user', 'role', 'created_at']);
    }

    public function query(Form $model, Request $request)
    {
        $usr    = \Auth::user();
        $roleId = $usr->roles->first()->id;
        $userId = $usr->id;
        
        if (\Auth::user()->can('access-all-form')) {
            $model = $model->newQuery()->select('forms.*', 'form_categories.name as category_name')
            ->join('form_categories', 'form_categories.id', '=', 'forms.category_id');
        }elseif ($usr->type == 'Super Admin') {
            $model =   $model->newQuery()
                ->select('forms.*', 'form_categories.name AS category_name')
                ->leftJoin('form_categories', 'form_categories.id', '=', 'forms.category_id')
                ->where(function ($query)   use ($roleId, $userId) {
                    $query->whereIn('forms.id', function ($query) use ($roleId) {
                        $query->select('form_id')->from('assign_forms_roles')->where('role_id', $roleId);
                    })->orWhereIn('forms.id', function ($query) use ($userId) {
                        $query->select('form_id')->from('assign_forms_users')->where('user_id', $userId);
                    });
                });
        } elseif ($usr->type == 'Admin') {
            $model = $model->newQuery()->select('forms.*', 'form_categories.name as category_name')
                ->join('form_categories', 'form_categories.id', '=', 'forms.category_id')->where('forms.created_by', Auth::user()->admin_id);
        } else {
            $model = $model->newQuery()
                ->select('forms.*', 'form_categories.name AS category_name')
                ->leftJoin('form_categories', 'form_categories.id', '=', 'forms.category_id')
                ->where(function ($query)   use ($roleId, $userId) {
                    $query->whereIn('forms.id', function ($query) use ($roleId) {
                        $query->select('form_id')->from('assign_forms_roles')->where('role_id', $roleId);
                    })->orWhereIn('forms.id', function ($query) use ($userId) {
                        $query->select('form_id')->from('assign_forms_users')->where('user_id', $userId);
                    });
                });
        }

        if ($request->start_date && $request->end_date) {
            $model->whereBetween('forms.created_at', [$request->start_date, $request->end_date]);
        }
        if ($request->categories) {
            $model->whereIn('forms.category_id', $request->categories);
        }
        if ($request->role) {
            $model->Join('assign_forms_roles', 'forms.id', '=', 'assign_forms_roles.form_id')
                ->where('assign_forms_roles.role_id', $request->role);
        }
        return $model;
    }

    public function html()
    {
        $pdfButton     = [];
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            $pdfButton = ["extend" => "pdf", "text" => '<i class="fas fa-file-pdf"></i>' . __('PDF'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]];
        }
        return $this->builder()
            ->setTableId('forms-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var filter = $("#filterdate").val();
                    var spilit = filter.split(" to ");
                    d.start_date = spilit[0];
                    d.end_date = spilit[1];

                    var category = $(".category").val();
                    d.categories = category;

                    var roles = $(".roles").val();
                    d.role = roles;
                }',
            ])
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
                $("body").on("click", "#applyfilter", function() {
                    $("#forms-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $(".created_at").val("");
                    $(".category").val([]).trigger("change");
                    $(".roles").val("").trigger("change");
                    $("#forms-table").DataTable().draw();
                });
                var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                searchInput.removeClass(\'form-control form-control-sm\');
                searchInput.addClass(\'dataTable-input\');
                var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
            }')
            ->parameters([
                "dom" =>  "
                <'dataTable-top row'<'dataTable-dropdown page-dropdown col-lg-2 col-sm-12'l><'dataTable-botton table-btn col-lg-6 col-sm-12'B><'dataTable-search tb-search col-lg-3 col-sm-12'f>>
                <'dataTable-container dropdown-icon'<'col-sm-12'tr>>
                <'dataTable-bottom row table-footer'<'col-sm-5 footer-result'i><'col-sm-7 footer-pagination'p>>
                               ",
                'buttons'   => [
                    ['extend' => 'create', 'className' => 'btn btn-light-primary no-corner me-1 add_module', 'action' => " function ( e, dt, node, config ) {
                        window.location = '" . route('forms.add') . "';
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

    protected function getColumns()
    {
        return [
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('title')->title(__('Title')),
            Column::make('category_id')->title(__('Category')),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::computed('action')->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Forms_' . date('YmdHis');
    }
}
