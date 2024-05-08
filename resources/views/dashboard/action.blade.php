<span>
    @can('edit-dashboard-widget')
    <a class="btn btn-primary btn-sm" href="javascript:void(0);" id="edit-dashboard"
        data-url="{{ route('dashboard.edit',$dashboard->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete-dashboard-widget')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['dashboard.destroy', $dashboard->id],
        'id' => 'delete-form-' . $dashboard->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $dashboard->id }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
            class="mr-0 ti ti-trash"></i></a>
    {!! Form::close() !!}
    @endcan
</span>
