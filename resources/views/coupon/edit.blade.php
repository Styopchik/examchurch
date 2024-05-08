{!! Form::model($coupon, [
    'route' => ['coupon.update', $coupon->id],
    'method' => 'Put',
    'enctype' => 'multipart/form-data',
    'data-validate',
]) !!}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('discount_type', __('Discount Type'), ['class' => 'form-label']) }}
        <select name="discount_type" id="discount_type" class="form-control" data-trigger>
            <option value="">{{ __('Select discount type') }}</option>
            <option value="flat" {{ $coupon->discount_type == 'flat' ? 'selected' : '' }}>
                {{ __('Flat') }}</option>
            <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>
                {{ __('Percentage') }}</option>
        </select>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('discount', __('Discount'), ['class' => 'form-label']) }}
                <div class="input-group">
                    {{ Form::number('discount', null, ['class' => 'form-control', 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('limit', __('Limit'), ['class' => 'form-label']) }}
                <div class="input-group">
                    {{ Form::number('limit', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
        {{ Form::text('code', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter code']) }}
    </div>
</div>
<div class="modal-footer">
    <div class="float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
    </div>
</div>
{!! Form::close() !!}
