@can('edit-category')
    <a class="btn btn-sm small btn-primary" href="{{ route('blogs-category.edit', $category->id) }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}">
        <i class="text-white ti ti-edit"></i>
    </a>
@endcan
@can('delete-category')
    {!! Form::open([
        'method' => 'DELETE',
        'class' => 'd-inline',
        'route' => ['blogs-category.destroy', $category->id],
        'id' => 'delete-form-' . $category->id,
    ]) !!}
    <a href="#" class="btn btn-sm small btn-danger show_confirm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        id="delete-form-{{ $category->id }}" data-bs-original-title="{{ __('Delete') }}">
        <i class="text-white ti ti-trash"></i>
    </a>
    {!! Form::close() !!}
@endcan
