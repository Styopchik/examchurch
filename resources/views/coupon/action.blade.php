<span>
    @can('show-coupon')
        <a class="btn btn-info btn-sm" href="{{ route('coupon.show', $coupon->id) }}" id="show-user" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-bs-original-title="{{ __('Show') }}"><i class="ti ti-eye"></i></a>
    @endcan
    @can('edit-coupon')
        <a class="btn btn-primary btn-sm coupon_edit" href="#" data-url="{{ route('coupon.edit', $coupon->id) }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete-coupon')
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['coupon.destroy', $coupon->id],
            'id' => 'delete-form-' . $coupon->id,
            'class' => 'd-inline',
        ]) !!}
        <a href="#" class="btn btn-sm btn-danger show_confirm" id="delete-form-{{ $coupon->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                class="mr-0 ti ti-trash"></i></a>
        {!! Form::close() !!}
    @endcan
</span>
