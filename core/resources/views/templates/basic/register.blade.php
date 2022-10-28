@extends($activeTemplate.'layouts.frontend')

@php
    $bg = getContent('login.content',true)->data_values;
@endphp

@section('content')

    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-100 pb-100">
        <div class="container">
            <div class="account-area">
                <div class="row justify-content-center">
                    <div class="col-lg-6 bg_img d-flex flex-wrap justify-content-center align-items-center"
                         data-background="{{getImage('assets/images/frontend/login/'.$bg->background_image,'1920x1080')}}">
                        <div class="account-content text-center px-5 py-4">
                            <h2 class="text-white title">@lang('Welcome to') {{$general->sitename}}</h2>
                            <p class="para-white mt-3">@lang('Please Provide valid informations')</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="account-wrapper">
                            <ul class="nav nav-tabs account-tab-nav" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="publisher-tab" data-toggle="tab" href="#publisher"
                                       role="tab" aria-controls="publisher" aria-selected="true">@lang('Publisher')</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="advertiser-tab" data-toggle="tab" href="#advertiser"
                                       role="tab" aria-controls="advertiser"
                                       aria-selected="false">@lang('Advertiser')</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="publisher" role="tabpanel"
                                     aria-labelledby="publisher-tab">
                                    <form class="account-form" method="POST" action="{{route('publisher.register')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label>@lang('Name') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="name" placeholder="Full Name" class="form-control"
                                                   value="{{old('name')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Email Address') <sup class="text-danger">*</sup></label>
                                            <input type="email" name="email" placeholder="Email address"
                                                   class="form-control" value="{{old('email')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Username') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="username" placeholder="User Name"
                                                   class="form-control" value="{{old('username')}}" required>
                                        </div>
                                        <div class="form-group country-code">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <select name="country_code" class="nic-select">
                                                        @include('partials.country_code')
                                                    </select>
                                                </span>
                                                </div>
                                                <input type="text" name="phone" class="form-control"
                                                       placeholder="@lang('Your Phone Number')">
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="Country" name="country" class="form-control"
                                                   value="{{ old('country') }}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="City" name="city" class="form-control"
                                                   value="{{ old('city') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Password') <sup class="text-danger">*</sup></label>
                                            <input type="password" name="password" placeholder="Enter Password"
                                                   class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Confirm Password') <sup class="text-danger">*</sup></label>
                                            <input type="password" name="password_confirmation"
                                                   placeholder="Confirm Password" class="form-control" required>
                                        </div>

                                        @include($activeTemplate.'partials.custom-captcha')
                                        <div class="form-group row">
                                            <div class="col-md-12 ">
                                                @php echo recaptcha() @endphp
                                            </div>
                                        </div>
                                        <button type="submit" class="cmn-btn w-100">@lang('SignUp')</button>
                                    </form>
                                </div>
                                <div class="tab-pane fade show" id="advertiser" role="tabpanel"
                                     aria-labelledby="advertiser-tab">
                                    <form class="account-form" method="POST" action="{{route('advertiser.register')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label>@lang('Name') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="name" placeholder="Full Name" class="form-control"
                                                   value="{{old('name')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Email Address') <sup class="text-danger">*</sup></label>
                                            <input type="email" name="email" placeholder="Email address"
                                                   class="form-control" value="{{old('email')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Username') <sup class="text-danger">*</sup></label>
                                            <input type="text" name="username" placeholder="User Name"
                                                   class="form-control" value="{{old('username')}}" required>
                                        </div>
                                        <div class="form-group country-code">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <select name="country_code" class="nic-select">
                                                        @include('partials.country_code')
                                                    </select>
                                                </span>
                                                </div>
                                                <input type="text" name="mobile" class="form-control"
                                                       placeholder="@lang('Your Phone Number')">
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="Country" name="country" class="form-control"
                                                   value="{{ old('country') }}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="City" name="city" class="form-control"
                                                   value="{{ old('city') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Password') <sup class="text-danger">*</sup></label>
                                            <input type="password" name="password" placeholder="Enter Password"
                                                   class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Confirm Password') <sup class="text-danger">*</sup></label>
                                            <input type="password" name="password_confirmation"
                                                   placeholder="Confirm Password" class="form-control" required>
                                        </div>

                                        @include($activeTemplate.'partials.custom-captcha')
                                        <div class="form-group row">
                                            <div class="col-md-12 ">
                                                @php echo recaptcha() @endphp
                                            </div>
                                        </div>
                                        <button type="submit" class="cmn-btn w-100">@lang('SignUp')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- account section end -->
@endsection

@push('script')
    <script>
        "use strict";
            @if($country_code)
        var t = $(`option[data-code={{ $country_code }}]`).attr('selected', '');
        @endif
        $('select[name=country_code]').change(function () {
            $('input[name=country]').val($('select[name=country_code] :selected').data('country'));
        }).change();

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span style="color:red;">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }

        function verifyCaptcha() {
            document.getElementById('g-recaptcha-error').innerHTML = '';
        }
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
            border: none;
            outline: none;
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

        .nice-select:active, .nice-select.open, .nice-select:focus {
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

        .nice-select .option:hover, .nice-select .option.focus, .nice-select .option.selected.focus {
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
    </style>
@endpush
