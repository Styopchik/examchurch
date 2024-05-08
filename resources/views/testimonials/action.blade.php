@can('edit-testimonial')
    <a class="btn btn-sm small btn-primary" href="{{ route('testimonials.edit', $row->id) }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"
        aria-label="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
@endcan
@can('delete-testimonial')
{!! Form::open([
    'method' => 'DELETE',
    'class' => 'd-inline',
    'route' => ['testimonials.destroy', $row->id],
    'id' => 'delete-form-' . $row->id,
]) !!}
<a href="#" class="btn btn-sm small btn-danger show_confirm" data-bs-toggle="tooltip"
    data-bs-placement="bottom" id="delete-form-{{ $row->id }}" data-bs-original-title="{{ __('Delete') }}"
    aria-label="{{ __('Delete') }}"><i class="ti ti-trash"></i></a>
{!! Form::close() !!}
@endcan
