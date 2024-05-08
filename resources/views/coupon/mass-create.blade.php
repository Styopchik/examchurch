{!! Form::open([
    'route' => 'coupon.mass.store',
    'method' => 'Post',
    'enctype' => 'multipart/form-data',
    'data-validate',
    'novalidate',
]) !!}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('mass_create', __('Mass Create'), ['class' => 'form-label']) }}
        {{ Form::number('mass_create', null, ['class' => 'form-control', 'placeholder' => __('Mass create'), 'required']) }}
        <small>{{ __('Note: Maximum 50 Number Required') }}</small>
    </div>
    <div class="form-group">
        {{ Form::label('discount_type', __('Discount Type'), ['class' => 'form-label']) }}
        <select name="discount_type" class="form-control" data-trigger>
            <option value="">{{ __('Select discount type') }}</option>
            <option value="flat">{{ __('Flat') }}</option>
            <option value="percentage">{{ __('Percentage') }}</option>
        </select>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('discount', __('Discount'), ['class' => 'form-label']) }}
                {{ Form::number('discount', null, ['class' => 'form-control', 'placeholder' => __('Discount'), 'required', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('limit', __('Limit'), ['class' => 'form-label']) }}
                {{ Form::number('limit', null, ['class' => 'form-control', 'placeholder' => __('Limit'), 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
    </div>
</div>
{!! Form::close() !!}
