{!! Form::open([
    'route' => 'coupon.store',
    'method' => 'Post',
    'enctype' => 'multipart/form-data',
    'data-validate',
    'novalidate',
]) !!}
<div class="modal-body">
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
    <div class="mb-1 form-group">
        {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
        <div class="d-flex radio-check">
            <div class="form-check form-check-inline col-md-6">
                <input type="radio" id="manual_code" value="manual" name="icon_input" class="form-check-input code"
                    checked="checked">
                <label class="custom-control-label" for="manual_code">{{ __('Manual') }}</label>
            </div>
            <div class="form-check form-check-inline col-md-6">
                <input type="radio" id="auto_code" value="auto" name="icon_input" class="form-check-input code">
                <label class="custom-control-label" for="auto_code">{{ __('Auto Generate') }}</label>
            </div>
        </div>
    </div>
    <div class="form-group d-block" id="manual">
        <input class="form-control font-uppercase" name="manualCode" type="text"
            placeholder="{{ __('Enter code') }}">
    </div>
    <div class="form-group d-none" id="auto">
        <div class="row">
            <div class="col-md-10">
                <input class="form-control" name="autoCode" type="text" placeholder="{{ __('Generate code') }}"
                    id="auto-code">
            </div>
            <div class="col-md-2">
                <a href="#" class="btn btn-primary" id="code-generate"><i class="ti ti-history"></i></a>
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
