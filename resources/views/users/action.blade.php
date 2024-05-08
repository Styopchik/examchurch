<span>
    @if (\Auth::user()->type == 'Super Admin')
        @can('plan-upgrade-user')
            <a href="#" id="user-plan" data-url="{{ route('user.plan', $user->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Upgrade Plan') }}" class="btn btn-success btn-sm"><i
                    class="ti ti-shopping-cart"></i></a>
        @endcan
    @endif
    @can('impersonate-user')
        <a class="btn btn-secondary btn-sm" href="{{ route('users.impersonate', $user->id) }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-bs-original-title="{{ __('Impersonate') }}"
            aria-label="{{ __('Impersonate') }}">
            <i class="ti ti-new-section"></i>
        </a>
    @endcan
    @can('phone-verified-user')
        @if ($user->phone_verified_at != '')
            <a class="btn btn-info btn-sm" href="{{ route('user.phoneverified', $user->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Phone Verified') }}">
                <i class="ti ti-message-circle"></i></a>
        @else
            <a class="btn btn-warning btn-sm" href="{{ route('user.phoneverified', $user->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Phone Unverified') }}">
                <i class="ti ti-message-circle"></i></a>
        @endif
    @endcan
    @can('email-verified-user')
        @if ($user->email_verified_at)
            <a class="btn btn-info btn-sm" href="{{ route('user.verified', $user->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Email Verified') }}">
                <i class="ti ti-mail"></i></a>
        @else
            <a class="btn btn-warning btn-sm" href="{{ route('user.verified', $user->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Email Unverified') }}">
                <i class="ti ti-mail-forward"></i></a>
        @endif
    @endcan
    @can('edit-user')
        <a class="btn btn-primary btn-sm" href="javascript:void(0);" id="edit-user" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"
            data-url="{{ route('users.edit', $user->id) }}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete-user')
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['users.destroy', $user->id],
            'id' => 'delete-form-' . $user->id,
            'class' => 'd-inline',
        ]) !!}
        <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $user->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                class="mr-0 ti ti-trash"></i></a>
        {!! Form::close() !!}
    @endcan
</span>
