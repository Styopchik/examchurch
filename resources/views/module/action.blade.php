@can('edit-module')
    <a class="text-white d-initial btn bg-primary btn-sm edit_module" href="{{ route('module.edit', $module->id) }}"
        id="edit-module" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"><i
            class="ti ti-edit"></i></a>
@endcan
@can('delete-module')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['module.destroy', $module->id],
        'id' => 'delete-form-' . $module->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $module->id }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i class="ti ti-trash"></i></a>
    {!! Form::close() !!}
@endcan
