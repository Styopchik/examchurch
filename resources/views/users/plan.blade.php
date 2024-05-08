<div class="modal-body">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td>
                                    <h6>{{ $plan->name }} ({{ Utility::amountFormat($plan->price) }}) /
                                        {{ $plan->duration . ' ' . $plan->durationtype }}
                                    </h6>
                                </td>
                                <td>{{ __('Users :') }} {{ $plan->max_users }}</td>
                                <td>{{ __('Roles :') }} {{ $plan->max_roles }}</td>
                                <td>{{ __('Forms :') }} {{ $plan->max_form }}</td>
                                <td>{{ __('Bookings :') }} {{ $plan->max_booking }}</td>
                                <td>{{ __('Documents :') }} {{ $plan->max_documents }}</td>
                                <td>{{ __('Polls :') }} {{ $plan->max_polls }}</td>
                                <td>
                                    @if ($user->plan_id != $plan->id)
                                        <a href="{{ route('user.plan.assign', [$user->id, $plan->id]) }}"
                                            class="my-auto btn btn-primary btn-sm rounded-pill w-100"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="{{ __('Click to Upgrade Plan') }}"><i
                                                class="fas fa-cart-plus"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>