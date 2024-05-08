@can('edit-email-template')
    <a class="btn btn-primary btn-sm" href="{{ route('email-template.edit', $row->id) }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
@endcan
