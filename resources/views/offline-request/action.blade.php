@if ($offlinerequest->is_approved == 0)
    <a class="btn btn-primary btn-sm" href="{{ route('offlinerequest.status', $offlinerequest->id) }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Approve') }}">
        <i class="ti ti-checks"></i>
    </a>
    <a class="btn btn-warning btn-sm reason" data-id="{{ $offlinerequest->id }}"
        data-url="{{ route('offline.disapprove.status', $offlinerequest->id) }}" href="javascript:void(0)"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Disapprove') }}">
        <i class="ti ti-ban"></i></a>
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['offline.destroy', $offlinerequest->id],
        'id' => 'delete-form-' . $offlinerequest->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $offlinerequest->id }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
            class="mr-0 ti ti-trash"></i></a>
    {!! Form::close() !!}
@endif
