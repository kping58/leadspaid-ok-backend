@extends($activeTemplate.'layouts.advertiser.frontend')
@php
$user = auth()->guard('advertiser')->user();
@endphp
@section('panel')

<section>
    <div class="container">
        <div class="account-area">
            <div class="row justify-content-center">

                <div class="col-lg-12">
                    <div class="profile account-wrapper">
                        <div class="tab-content mt-5" id="myTabContent">
                            <form method="POST" id="advertiser_form" action="{{route('advertiser.profile.update')}}">
                                @csrf
                                <div class="row">
                                <div class="mb-4 col-md-6" style="overflow: inherit;">
                                    <div class="font-weight-bolder text-body"> Basic Information
                                    </div>
                                    <div class="card-body px-0">
                                        <div class="form-group ">
                                            <label class="d-none">@lang('Company Name')</label>
                                            <input type="text" class="form-control rounded-0" name="company_name" value="{{ auth()->guard('advertiser')->user()->company_name }}" placeholder="Company Name">
                                        </div>
                                        <div class="form-group">
                                            <label class="d-none">@lang('Full Name') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="name" placeholder="Full Name" class="form-control rounded-0" value="{{auth()->guard('advertiser')->user()->name}}" required>
                                        </div>
                                        <div class="form-group country-code">
                                            <label class="d-none">@lang('Mobile') <sup class="text-danger">*</sup></label>
                                            <div class="Rg_advts_number">
                                                <select name="country_code" class="form-select rounded-0">
                                            @include('partials.country_code')
                                            </select>
                                                <input type="text" name="mobile" value="{{auth()->guard('advertiser')->user()->mobile}}" class="form-control rounded-0" required placeholder="@lang('Your Phone Number')">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="mb-4 col-md-6">
                                    <div class="font-weight-bolder text-body"> Address Information
                                    </div>
                                    <div class="card-body px-0">
                                        <div class="form-group ">
                                            <label class="d-none">@lang('Address')<sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control rounded-0" name="billed_to" value="{{auth()->guard('advertiser')->user()->billed_to}}" required placeholder="Address">
                                        </div>
                                       <!--  <div class="form-group">
                                            <label class="d-none">@lang('Billing Email Address') <sup class="text-danger">*</sup></label>
                                            <input type="email" name="email" placeholder="Billing Email address" class="form-control rounded-0" value="{{auth()->guard('advertiser')->user()->email}}" required>
                                        </div> -->
                                        <div class="form-group">
                                            <input type="text" placeholder="City" name="city" class="form-control rounded-0" value="{{auth()->guard('advertiser')->user()->city}}" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="Postal Code" name="postal_code" class="form-control rounded-0" value="{{auth()->guard('advertiser')->user()->postal_code}}" required>
                                        </div>
                                        <div class="form-group">
                                            <select class="custom-select rounded-0 mr-sm-2 form-control" value="{{auth()->guard('advertiser')->user()->country}}" required name="country">
                                                @foreach ($countries as $country)
                                                <option @if($user->country === $country->country_name)
                                                    selected="selected" @endif
                                                    value=" {{ $country->country_name }} " label=" {{
                                                        $country->country_name }} ">
                                                    {{ $country->country_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <div class="font-weight-bolder text-body"> User Information
                                    </div>
                                    <div class="card-body px-0">
                                        <div class="form-group">
                                            <label class="d-none">@lang('Username') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="username" placeholder="User Name" class="form-control rounded-0" readonly value="{{auth()->guard('advertiser')->user()->username}}" required>
                                        </div>
                                    </div>
                                    @include($activeTemplate.'partials.custom-captcha')
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group row m-0">
                                        <div class="col-md-12 ">
                                            @php echo recaptcha() @endphp
                                        </div>
                                    </div>
                                    <button type="submit" class="box--shadow1 rounded-0 btn btn--primary btn-lg text--small Rg_advts_my_btn">@lang('Save Changes')</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('breadcrumb-plugins')
<a href="{{route('advertiser.password')}}" class="btn btn-sm btn--primary box--shadow1 text--small profile_pass_setting rounded-0"><i class="fa fa-key"></i>@lang('Password Setting')</a>
@endpush

@push('script')

<script>
    'use strict'

    function proPicURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = $(input).parents('.thumb').find('.profilePicPreview');
                $(preview).css('background-image', 'url(' + e.target.result + ')');
                $(preview).addClass('has-image');
                $(preview).hide();
                $(preview).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".profilePicUpload").on('change', function() {
        proPicURL(this);
    });

    $(".remove-image").on('click', function() {
        $(this).parents(".profilePicPreview").css('background-image', 'none');
        $(this).parents(".profilePicPreview").removeClass('has-image');
        $(this).parents(".thumb").find('input[type=file]').val('');
    });

    $("form").on("change", ".file-upload-field", function() {
        $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/, ''));
    });

    document.addEventListener('DOMContentLoaded', function(e) {
        FormValidation.formValidation(document.querySelector('#advertiser_form'), {
            fields: {
                company_name: {
                    validators: {
                        stringLength: {
                            min: 3,
                            message: 'Company Name is required.',
                        }
                    },
                },

                name: {
                    validators: {
                        notEmpty: {
                            message: 'Company Name is required.',
                        },
                        stringLength: {
                            min: 3,
                            message: 'Company Name is required.',
                        },
                        regexp: {
                            regexp: /^[a-z A-Z]+$/,
                            message: 'Full Name Invalid.',
                        },
                    },
                },
                mobile: {
                    validators: {
                        notEmpty: {
                            message: 'Phone is required.',
                        },
                        stringLength: {
                            min: 6,
                            message: 'Phone is required.',
                        },
                    },
                },
                billed_to: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill address to.',
                        },
                        stringLength: {
                            min: 3,
                            message: 'Please fill address to.',
                        },
                    },
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill vaild Email.',
                        },
                        emailAddress: {},
                    },
                },
                city: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill city.',
                        },
                        stringLength: {
                            min: 2,
                            message: 'Please fill city.',
                        },
                        regexp: {
                            regexp: /^[a-z A-Z]+$/,
                            message: 'City Invalid.',
                        },
                    },
                },
                country: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required.',
                        }
                    },
                },
                username: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill Username.',
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_.]+$/,
                            message: 'Username should not contain special characters.',
                        },
                    },
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill Password',
                        },
                    },
                },
                password_confirmation: {
                    validators: {
                        notEmpty: {
                            message: 'Please fill Confirm Password',
                        },
                        checkConfirmation: {
                            message: 'Passowrd Mismatch',
                            callback: function(input) {
                                return document.querySelector("#advertiser_form").querySelector('[name="password"]').value === input.value;
                            },
                        },
                    },
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                icon: new FormValidation.plugins.Icon({
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh',
                }),
                alias: new FormValidation.plugins.Alias({
                    checkConfirmation: 'callback'
                }),
            },
        }).on('core.form.valid', function() {
            document.querySelector('#advertiser_form').submit();

        });
    });
</script>

@endpush


@push('style')
<style type="text/css">
    .country-code .input-group-prepend .input-group-text {
        background: #fff !important;
    }

    .country-code select {
        border: none;
    }

    .country-code select:focus {
        outline: none;
        box-shadow: none;
    }

    .nice-select {
        -webkit-tap-highlight-color: transparent;
        background-color: #fff;
        border-radius: 5px;
        border: solid 1px #e8e8e8;
        box-sizing: border-box;
        clear: both;
        cursor: pointer;
        display: block;
        float: left;
        font-family: inherit;
        font-size: 14px;
        font-weight: normal;
        height: 42px;
        line-height: 40px;
        outline: none;
        padding-left: 18px;
        padding-right: 30px;
        position: relative;
        text-align: left !important;
        -webkit-transition: all 0.2s ease-in-out;
        transition: all 0.2s ease-in-out;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        white-space: nowrap;
        width: auto;
    }

    .nice-select:hover {
        border-color: #dbdbdb;
    }

    .nice-select:active,
    .nice-select.open,
    .nice-select:focus {
        border-color: #999;
    }

    .nice-select:after {
        border-bottom: 2px solid #999;
        border-right: 2px solid #999;
        content: '';
        display: block;
        height: 5px;
        margin-top: -4px;
        pointer-events: none;
        position: absolute;
        right: 12px;
        top: 50%;
        -webkit-transform-origin: 66% 66%;
        -ms-transform-origin: 66% 66%;
        transform-origin: 66% 66%;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        -webkit-transition: all 0.15s ease-in-out;
        transition: all 0.15s ease-in-out;
        width: 5px;
    }

    .nice-select.open:after {
        -webkit-transform: rotate(-135deg);
        -ms-transform: rotate(-135deg);
        transform: rotate(-135deg);
    }

    .nice-select.open .list {
        opacity: 1;
        pointer-events: auto;
        -webkit-transform: scale(1) translateY(0);
        -ms-transform: scale(1) translateY(0);
        transform: scale(1) translateY(0);
    }

    .nice-select.disabled {
        border-color: #ededed;
        color: #999;
        pointer-events: none;
    }

    .nice-select.disabled:after {
        border-color: #cccccc;
    }

    .nice-select.wide {
        width: 100%;
    }

    .nice-select.wide .list {
        left: 0 !important;
        right: 0 !important;
    }

    .nice-select.right {
        float: right;
    }

    .nice-select.right .list {
        left: auto;
        right: 0;
    }

    .nice-select.small {
        font-size: 12px;
        height: 36px;
        line-height: 34px;
    }

    .nice-select.small:after {
        height: 4px;
        width: 4px;
    }

    .nice-select.small .option {
        line-height: 34px;
        min-height: 34px;
    }

    .nice-select .list {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 0 1px rgba(68, 68, 68, 0.11);
        box-sizing: border-box;
        margin-top: 4px;
        opacity: 0;
        overflow: hidden;
        padding: 0;
        pointer-events: none;
        position: absolute;
        top: 100%;
        left: -11;
        -webkit-transform-origin: 50% 0;
        -ms-transform-origin: 50% 0;
        transform-origin: 50% 0;
        -webkit-transform: scale(0.75) translateY(-21px);
        -ms-transform: scale(0.75) translateY(-21px);
        transform: scale(0.75) translateY(-21px);
        -webkit-transition: all 0.2s cubic-bezier(0.5, 0, 0, 1.25), opacity 0.15s ease-out;
        transition: all 0.2s cubic-bezier(0.5, 0, 0, 1.25), opacity 0.15s ease-out;
        z-index: 9;
    }

    .nice-select .list:hover .option:not(:hover) {
        background-color: transparent !important;
    }

    .nice-select .option {
        cursor: pointer;
        font-weight: 400;
        line-height: 40px;
        list-style: none;
        min-height: 40px;
        outline: none;
        padding-left: 18px;
        padding-right: 29px;
        text-align: left;
        -webkit-transition: all 0.2s;
        transition: all 0.2s;
    }

    .nice-select .option:hover,
    .nice-select .option.focus,
    .nice-select .option.selected.focus {
        background-color: #f6f6f6;
    }

    .nice-select .option.selected {
        font-weight: bold;
    }

    .nice-select .option.disabled {
        background-color: transparent;
        color: #999;
        cursor: default;
    }

    .no-csspointerevents .nice-select .list {
        display: none;
    }

    .no-csspointerevents .nice-select.open .list {
        display: block;
    }


    .nice-select {
        height: 24px;
        line-height: 23px;
        border: none;
    }

    .nice-select .list {
        max-height: 200px;
        overflow-y: auto;
    }
    #advertiser_form .card-body .form-group label {
        font-size: 16px;
        color: #1a273a;
    }
    .Rg_advts_number {
    display: flex;
    flex-wrap: wrap;
}
.Rg_advts_number .form-select {
    background: #f1f1f2 url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e") no-repeat right .75rem center/15px 10px !important;
    appearance: none;
}
.Rg_advts_number select {
    flex: 0 0 25%;
    width: 25%;
    border: 1px solid #ced4da;
}
.Rg_advts_number input {
    flex: 0 0 75%;
    width: 75%;
}
#advertiser_form .fv-plugins-icon-container i {
    top: 13px;
}
#advertiser_form .form-group input, #advertiser_form .form-group select {
    background: #fff;
    display: block;
    font-size: 19px !important;
    padding: 16px 24px;
    line-height: normal;
    height: unset;
    text-transform: capitalize;
}
.profile_pass_setting {
    font-size: 18px !important;
    padding: .375rem .75rem;
}
.Rg_advts_my_btn {    
    font-size: 18px !important;
    padding: .375rem .75rem;
}
</style>
@endpush