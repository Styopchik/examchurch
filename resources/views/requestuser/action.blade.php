@if ($requestuser->is_approved == 0)
    {{-- @if ($requestuser->payStatus->status == 1 ||
        $requestuser->payStatus->plan_id == 1 ||
        $requestuser->payStatus->status == 3) --}}
        <a class="btn btn-success btn-sm" href="{{ route('approverequestuser.status', $requestuser->id) }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Approve') }}">
            <i class="ti ti-checks"></i></a>
    {{-- @endif --}}
    <a class="btn btn-danger btn-sm reason" data-id="{{ $requestuser->id }}"
        data-url="{{ route('requestuser.disapprove', $requestuser->id) }}" href="javascript:void(0)"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Disapprove') }}">
        <i class="ti ti-ban"></i></a>
    @if ($requestuser->payStatus->status == 1 ||
        $requestuser->payStatus->plan_id == 1 ||
        $requestuser->payStatus->status == 3)
        <a class="btn btn-primary btn-sm" href="{{ route('requestuser.edit', $requestuser->id) }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"><i
                class="ti ti-edit"></i></a>
    @endif
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['requestuser.destroy', $requestuser->id],
        'id' => 'delete-form-' . $requestuser->id,
        'class' => 'd-inline',
    ]) !!}

    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $requestuser->id }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
            class="ti ti-trash mr-0"></i></a>
    {!! Form::close() !!}
@endif
