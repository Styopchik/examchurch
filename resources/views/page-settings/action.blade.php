@can('edit-page-setting')
    <a class="btn btn-sm small btn-primary" href="{{ route('page-setting.edit', $row->id) }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}">
        <i class="text-white ti ti-edit"></i>
    </a>
@endcan
@can('delete-page-setting')
    {!! Form::open([
        'method' => 'DELETE',
        'class' => 'd-inline',
        'route' => ['page-setting.destroy', $row->id],
        'id' => 'delete-form-' . $row->id,
    ]) !!}
    <a href="javascript:void(0);" class="btn btn-sm small btn-danger show_confirm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        id="delete-form-1" data-bs-original-title="{{ __('Delete') }}">
        <i class="text-white ti ti-trash"></i>
    </a>
    {!! Form::close() !!}
@endcan
