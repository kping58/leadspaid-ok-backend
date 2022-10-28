<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\Advertiser;
use App\CreateAd;
use App\Transaction;
use App\GeneralSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\ApiOperations\Create;

class AdvertiserController extends Controller
{
    public function allAdvertiser()
    {
        $page_title = 'All advertiser';
        $empty_message = 'No advertiser';
        $advertisers = Advertiser::latest()->paginate(15);
        return view('admin.advertiser.all',compact('page_title','empty_message','advertisers'));
    }
    public function allActiveAdvertiser()
    {
        $page_title = 'All active advertiser';
        $empty_message = 'No advertiser';
        $advertisers = Advertiser::latest()->whereStatus(1)->paginate(15);
        return view('admin.advertiser.all',compact('page_title','empty_message','advertisers'));
    }

    public function advertiserDetails($id)
    {
        $page_title = 'Advertiser details';
        $advr = Advertiser::findOrFail($id);
        $totalDeposit = Deposit::where('user_id',$advr->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('user_id',$advr->id)->count();
        $totalAd = CreateAd::where('advertiser_id',$advr->id)->where('status',1)->count();
        return view('admin.advertiser.details',compact('advr','page_title','totalDeposit','totalTransaction','totalAd'));
    }

    public function loginHistory($id)
    {
        $user = Advertiser::findOrFail($id);
        $page_title = 'Advertiser Login History - ' . $user->username;
        $empty_message = 'No users login found.';
        $login_logs = $user->login_logs()->latest()->paginate(15);
        return view('admin.advertiser.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function advertiserAds($id)
    {
        $ads = CreateAd::where('advertiser_id',$id)->paginate(15);
        $empty_message = "No ads";
        $advr = Advertiser::findOrFail($id);
        $page_title = "Ads of  $advr->name";
        return view('admin.reports.ads',compact('ads','page_title','empty_message'));
    }

    public function advertiserUpdate(Request $request,$id)
    {
        $advr = Advertiser::findOrFail($id);
        $request->validate([
            'name' => 'required|max:60',
            'email' => 'required|email|max:160|unique:advertisers,email,' . $advr->id,
            'phone' => 'required|unique:advertisers,mobile,' . $advr->id,
            'city' => 'required',
            'country' => 'required'
        ]);

        $advr->update([
            'mobile' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'country' => $request->country,
            'status' => $request->status ? 1 : 0,
            'ev' => $request->ev ? 1 : 0,
            'sv' => $request->sv ? 1 : 0,
            'ts' => $request->ts ? 1 : 0,
            'tv' => $request->tv ? 1 : 0,
            
        ]);

        $notify[] = ['success', 'Advertiser detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }
    public function addSubBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|gt:0']);

        $advr = Advertiser::findOrFail($id);
        $amount = getAmount($request->amount);
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();

        if ($request->act) {
            $advr->balance +=  $amount;
            $advr->save();
            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $advr->username . ' balance'];


            $transaction = new Transaction();
            $transaction->user_id = $advr->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($advr->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Added Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($advr, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($advr->balance),
            ]);

        } else {
            if ($amount > $advr->balance) {
                $notify[] = ['error', $advr->username . ' has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $advr->balance = bcsub($advr->balance, $amount);
            $advr->save();

         

            $transaction = new Transaction();
            $transaction->user_id = $advr->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($advr->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtract Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($advr, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($advr->balance)
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $advr->username . ' balance'];
        }
        return back()->withNotify($notify);
    }

    public function emailUnverified()
    {
        $page_title = 'Email Unverified Users';
        $empty_message = 'No email unverified user found';
        $advertisers = Advertiser::emailUnverified()->latest()->paginate(15);
        return view('admin.advertiser.emailUnverified', compact('page_title', 'empty_message', 'advertisers'));
    }

    public function smsUnverified()
    {
        $page_title = 'SMS Unverified Users';
        $empty_message = 'No sms unverified user found';
        $advertisers = Advertiser::smsUnverified()->latest()->paginate(15);
        return view('admin.advertiser.smsUnverified', compact('page_title', 'empty_message', 'advertisers'));
    }


    public function advertiserBanned($id)
    {
        $Adv = Advertiser::findOrFail($id);
        $Adv->status = 2; 
        $Adv->update();
        $notify[] = ['success', 'Advertiser has been banned'];
        return redirect()->back()->withNotify($notify);
    }
    
    public function advertiserActive($id)
    {
        $Adv = Advertiser::findOrFail($id);
        $Adv->status = 1; 
        $Adv->update();
        $notify[] = ['success', 'Advertiser has been activated'];
        return redirect()->back()->withNotify($notify);
    }
    
    public function allBannedAdvertiser()
    {
        $page_title = 'All banned Advertisers';
        $empty_message = 'No data';
        $advertisers = Advertiser::latest()->whereStatus(2)->paginate(15);
        return view('admin.advertiser.banned',compact('page_title','empty_message','advertisers'));
    }

    public function showEmailAllForm()
    {
        $page_title = 'Send Email To All Advertiser';
        return view('admin.advertiser.email_all', compact('page_title'));
    }

   
    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (Advertiser::where('status', 1)->cursor() as $user) {
            send_general_email($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All advertiser will receive an email shortly.'];
        return back()->withNotify($notify);
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $users = Advertiser::where(function ($user) use ($search) {
            $user->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        });
        
        $advertisers = $users->paginate(getPaginate());
        $page_title = 'User Search - ' . $search;
        $empty_message = 'No search result found';
        return view('admin.advertiser.all', compact('page_title', 'search', 'empty_message', 'advertisers'));
    }
    
}

