@can('edit-role')
    <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}" id="edit-role" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Permissions') }}"><i class="ti ti-key"></i></a>
@endcan
@if (\Auth::user()->type != 'Super Admin')
    @can('edit-role')
        <a class="btn btn-primary btn-sm edit_role" href="javascript:void(0);" id="edit-role"
            data-url="{{ route('roles.edit', $role->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete-role')
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['roles.destroy', $role->id],
            'id' => 'delete-form-' . $role->id,
            'class' => 'd-inline',
        ]) !!}
        <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $role->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                class="mr-0 ti ti-trash"></i></a>
        {!! Form::close() !!}
    @endcan
@endif
