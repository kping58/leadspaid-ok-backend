<?php

namespace App\Http\Controllers\Advertiser;

use App\Deposit;
use App\CreateAd;
use Carbon\Carbon;
use App\PublisherAd;
use App\Transaction;
use App\GeneralSetting;
use Illuminate\Http\Request;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdvertiserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function dashboard()
    {
        $page_title = 'Advertiser Dashboard';
        $trxs = Transaction::all();

        $report['trx_months'] = collect([]);
        $report['d_months'] = collect([]);
        $report['trx_month_amount'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $user =  auth()->guard('advertiser')->user()->id;
        $transaction = Transaction::whereYear('created_at', '>=', Carbon::now()->subYear())
        ->selectRaw("SUM( CASE WHEN user_id = $user THEN amount END) as amount")
            ->selectRaw("DATE_FORMAT(created_at,'%M') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();

       $depositsMonth =     Deposit::whereYear('created_at', '>=', Carbon::now()->subYear())
            ->selectRaw("SUM( CASE WHEN user_id = $user THEN final_amo END) as depoAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();

        $transaction->map(function ($aaa) use ($report) {
            $report['trx_months']->push($aaa->months);
            $report['trx_month_amount']->push(getAmount($aaa->amount));
        });
        $depositsMonth->map(function ($aa) use ($report) {
            $report['d_months']->push($aa->months);
            $report['deposit_month_amount']->push(getAmount($aa->depoAmount));
        });

        $perDay = Transaction::whereUserId($user)->where('date',Carbon::now()->toDateString())->get();
        $yDay = Transaction::whereUserId($user)->where('date',Carbon::now()->subDays(1)->toDateString())->get();

        $todayReport = PublisherAd::where('advertiser_id',$user)->where('date',Carbon::now()->toDateString())->get();


        $totalDeposit = Deposit::where('user_id',auth()->guard('advertiser')->user()->id)->sum('amount');
        $totalTrx = Transaction::where('user_id',auth()->guard('advertiser')->user()->id)->count();
        $totalImp = CreateAd::where('advertiser_id',auth()->guard('advertiser')->user()->id)->get();
        return view($this->activeTemplate.'advertiser.dashboard',compact('page_title','todayReport','totalImp','trxs','report','totalDeposit','totalTrx','perDay','yDay'));
    }

    public function profile()
    {
        $page_title = 'Profile';
        $advertiser = Auth::guard('advertiser')->user();
        return view(activeTemplate().'advertiser.profile', compact('page_title', 'advertiser'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'city' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $user = Auth::guard('advertiser')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, 'assets/advertiser/images/profile/', '400X400', $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->city = $request->city;
        $user->update();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('advertiser.profile')->withNotify($notify);
    }


    public function password()
    {
        $page_title = 'Password Setting';
        $advertiser = Auth::guard('advertiser')->user();
        return view($this->activeTemplate.'advertiser.password', compact('page_title', 'advertiser'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = Auth::guard('advertiser')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password Do not match !!'];
            return back()->withErrors(['Invalid old password.']);
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        $notify[] = ['success', 'Password Changed Successfully.'];
        return redirect()->route('advertiser.password')->withNotify($notify);
    }


    public function depositHistory(Request $request)
    {
        $page_title = 'Deposit History';
        $search = $request->search;
        if($request->search){
            $page_title = 'Search Result - '.$search;
            $logs = auth()->guard('advertiser')->user()->deposits()->where('trx','like',"%$search%")->with(['gateway'])->latest()->paginate(getPaginate());
        } else {
            $logs = auth()->guard('advertiser')->user()->deposits()->with(['gateway'])->latest()->paginate(getPaginate());
        }
        $empty_message = 'No history found.';

        return view($this->activeTemplate . 'advertiser.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function trxLogs()
    {
        $trxs = Transaction::whereUserId(Auth::guard('advertiser')->user()->id)->latest()->paginate(15);
        $page_title = 'Transaction logs';
        $empty_message = 'No data';
        return view($this->activeTemplate .'advertiser.trxLogs',compact('trxs','page_title','empty_message'));
    }
    public function trxSearch(Request $request)
    {
        $page_title = 'Searched Results - '.$request->search;
        $empty_message = 'No data';
        $trxs = Transaction::where('trx','like',"%$request->search%")->paginate(15);
        return view($this->activeTemplate .'advertiser.trxLogs',compact('trxs','page_title','empty_message'));
    }

    public function perDay(Request $request)
    {
        $transactions = Transaction::whereUserId(Auth::guard('advertiser')->user()->id)->whereNotNull('date')->paginate(15);
        $page_title = 'Day to Day logs';
        $empty_message = 'No data';
        return view($this->activeTemplate .'advertiser.reports.perDay',compact('transactions','page_title','empty_message'));
    }

    public function perDateSearch(Request $request)
    {
        $page_title = "Search Result";
        $empty_message = "No data";
        $date = explode(' - ',$request->date);
        $notify[]=['error','Invalid Date'];
        if(!(@strtotime($date[0]))){
            return back()->withNotify($notify);
        }
        if(isset($date[1]) && !strtotime($date[1])){
            return back()->withNotify($notify);
        }
        if(count($date) == 1){
            $firstDate = Carbon::create($date[0])->format('Y-m-d');
            $transactions = Transaction::where('date','like',"% $firstDate%")->paginate(15);
        }else{
            $firstDate = Carbon::create($date[0])->format('Y-m-d');
            $secondDate = Carbon::create($date[1])->format('Y-m-d');
            $transactions = Transaction::whereUserId(auth()->guard('advertiser')->id())->whereBetween('date',[$firstDate,$secondDate])->paginate(15);
        }

        return view($this->activeTemplate .'advertiser.reports.perDay',compact('transactions','page_title','empty_message'));
    }


    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->guard('advertiser')->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Two Factor';
        return view($this->activeTemplate.'advertiser.twofactor', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->guard('advertiser')->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        if ($oneCode === $request->code) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();


            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            send_email($user, '2FA_ENABLE', [
                'operating_system' => $osBrowser['os_platform'],
                'browser' => $osBrowser['browser'],
                'ip' => $userAgent['ip'],
                'time' => $userAgent['time']
            ]);
            send_sms($user, '2FA_ENABLE', [
                'operating_system' => $osBrowser['os_platform'],
                'browser' => $osBrowser['browser'],
                'ip' => $userAgent['ip'],
                'time' => $userAgent['time']
            ]);


            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->guard('advertiser')->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();


            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            send_email($user, '2FA_DISABLE', [
                'operating_system' => $osBrowser['os_platform'],
                'browser' => $osBrowser['browser'],
                'ip' => $userAgent['ip'],
                'time' => $userAgent['time']
            ]);
            send_sms($user, '2FA_DISABLE', [
                'operating_system' => $userAgent['os_platform'],
                'browser' => $userAgent['browser'],
                'ip' => $userAgent['ip'],
                'time' => $userAgent['time']
            ]);


            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->with($notify);
        }
    }


}
