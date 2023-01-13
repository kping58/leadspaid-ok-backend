@extends($activeTemplate.'layouts.advertiser.frontend')

@section('panel')
    <div class="row justify-content-center mb-none-30">

        <div class="col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Change Password')</h5>

                    <form id="change_password" action="{{ route('advertiser.password.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-control-label">@lang('Old Password')</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="las la-key"></i></span>
                                </div>
                                <input class="form-control" type="password" name="old_password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">@lang('New password')</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="las la-key"></i></span>
                                </div>
                                <input class="form-control" type="password" name="password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">@lang('Confirm password')</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="las la-key"></i></span>
                                </div>
                                <input class="form-control" type="password" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block btn-lg Rg_advts_my_btn rounded-0">@lang('Save Changes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('advertiser.profile')}}" class="profile_pass_setting rounded-0 btn btn-sm btn--primary box--shadow1 text--small" ><i class="fa fa-user"></i>@lang('Profile Setting')</a>
@endpush

@push('style')
<style type="text/css">
 #change_password .form-group label {
        font-size: 16px;
        color: #1a273a;
    }
	
	.profile_pass_setting {
    font-size: 18px !important;
    padding: .375rem .75rem;
}

    .Rg_advts_my_btn {
    font-size: 18px !important;
    padding: .375rem .75rem;
    background-color: #4500dd!important;
    font-weight: 500;
}
	
</style>
@endpush
