@if (Auth::user()->type == 'Admin')
    @if ($formValue->form_edit_lock_status == 1)
        <a href="{{ route('form.fill.edit.lock', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Unlock') }}" title="{{ __('Unlock Edit') }}" class="btn btn-danger btn-sm"
            data-toggle="tooltip"><i class="ti ti-lock"></i> </a>
    @else
        <a href="{{ route('form.fill.edit.lock', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Lock') }}" title="{{ __('Lock Edit') }}" class="btn btn-success btn-sm"
            data-toggle="tooltip"><i class="ti ti-lock-open"></i> </a>
    @endif
@endif
@can('download-submitted-form')
    <a href="{{ route('download.form.values.pdf', $formValue->id) }}" class="btn btn-success btn-sm"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Download') }}"><i
            class="ti ti-file-download"></i></a>
@endcan
@can('show-submitted-form')
    <a href="{{ route('formvalues.show', $formValue->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('View') }}"><i class="ti ti-eye"></i>
        </span></a>
@endcan

@can('edit-submitted-form')
    @if (Auth::user()->type == 'Admin')
        <a href="{{ route('formvalues.edit', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Edit') }}" title="{{ __('Edit') }}" class="btn btn-primary btn-sm"
            data-toggle="tooltip"><i class="ti ti-edit"></i> </a>
    @elseif ($formValue->form_edit_lock_status == 0 && Auth::user()->type != 'Admin')
        <a href="{{ route('formvalues.edit', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Edit') }}" title="{{ __('Edit') }}" class="btn btn-primary btn-sm"
            data-toggle="tooltip"><i class="ti ti-edit"></i> </a>
    @endif
@endcan
@can('delete-submitted-form')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['formvalues.destroy', $formValue->id],
        'id' => 'delete-form-' . $formValue->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $formValue->id }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
            class="mr-0 ti ti-trash"></i></a>
    {!! Form::close() !!}
@endcan
