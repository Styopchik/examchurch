<span>
    @can('edit-plan')
        <a class="btn btn-primary btn-sm" href="plans/{{ $plan->id }}/edit"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"><i
                class="ti ti-edit"></i></a>
    @endcan
    @can('delete-plan')
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['plans.destroy', $plan->id],
            'id' => 'delete-form-' . $plan->id,
            'class' => 'd-inline',
        ]) !!}
        <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $plan->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                class="ti ti-trash mr-0"></i></a>
        {!! Form::close() !!}
    @endcan
</span>
