<?php

namespace App\Http\Controllers\Gateway\stripe_v3;

use App\Deposit;
use App\Country;
use App\Advertiser;
use App\Gateway;
use App\GatewayCurrency;
use App\TransactionAdvertiser;
use App\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;


class ProcessController extends Controller
{


    /*
     * Get Country Time from Country Name
     */
    public function TimezoneFromName($country_name = "Singapore")
    {
        $country_user = Country::where('country_name', $country_name)->latest()->first();
        $timezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $country_user->country_code);
        $d = new \DateTime("now", new \DateTimeZone($timezone[0]));
        $currentDateTime =  $d->format($d->format("Y-m-d H:i:s"));
        return $currentDateTime; // 2022-11-06 20:40:37
    }

    /*
     * Stripe V3 Gateway
     */
    public static function process($deposit)
    {
        $StripeJSAcc = json_decode($deposit->gateway_currency()->gateway_parameter);
        $alias = $deposit->gateway->alias;
        $general =  GeneralSetting::first();
        \Stripe\Stripe::setApiKey("$StripeJSAcc->secret_key");

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => $general->sitename,
                'description' => 'Deposit  with Stripe',
                'images' => [asset('assets/images/logoIcon/logo.png')],
                'amount' => $deposit->final_amo * 100,
                'currency' => "$deposit->method_currency",
                'quantity' => 1,
            ]],
            'cancel_url' => route(returnUrl()),
            'success_url' =>  route(returnUrl()),
        ]);

        $send['view'] = 'user.payment.stripe_v3';
        $send['session'] = $session;
        $send['StripeJSAcc'] = $StripeJSAcc;

        return json_encode($send);
    }

    /*
     * Stripe V3 js ipn
     */
    public function ipn(Request $request)
    {
        $StripeJSAcc = GatewayCurrency::where('gateway_alias', 'stripe_v3')->latest()->first();
        $gateway_parameter = json_decode($StripeJSAcc->gateway_parameter);

        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];


        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $data = Deposit::where('btc_wallet',  $session->id)->orderBy('id', 'DESC')->first();

            if ($data->status == 0) {
                PaymentController::userDataUpdate($data->trx);
            }
        }
        http_response_code(200);
    }


    public function charge(Request $request)
    {
        $users = Advertiser::get();

        foreach ($users as $user) {
            $currentDateTime = $this->TimezoneFromName($user->country);
            $newDateTime = date('h A', strtotime($currentDateTime));
            $expectedDateTime = \Carbon\Carbon::parse($this->TimezoneFromName($user->country));
            $notify[] = ['success', 'Successfully charged ' .  $currentDateTime];
            if ($newDateTime === "10 AM") {
                $previous_deposit = $user->wallet_deposit;
                $new_deposit =  $previous_deposit - $user->amount_used;
                $amount =  $user->total_budget - $new_deposit;
                if ($amount > 0) {
                    $deducting_amount = $amount + ($amount * 0.03);
                    $user->wallet_deposit = $new_deposit +  $amount;
                } else {
                    $deducting_amount = 0;
                    $user->wallet_deposit = $new_deposit;
                }

                $method = Gateway::where('alias', 'stripe')->firstOrFail();
                $gateway_parameter = json_decode($method->parameters);
                $stripe = new   \Stripe\StripeClient($gateway_parameter->secret_key->value);


                $setup_intent = $stripe->setupIntents->retrieve($user->card_session, []);
                $payment_method_id = $setup_intent->payment_method;
                $payment_method =  $stripe->paymentMethods->retrieve(
                    $payment_method_id,
                    []
                );
                $customer_id = $payment_method->customer;

                if ($deducting_amount >= 1) {
                    try {
                        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key->value);
                        \Stripe\PaymentIntent::create([
                            'amount' => $deducting_amount * 100,
                            'currency' => 'usd',
                            'customer' => $customer_id,
                            'payment_method' => $payment_method_id,
                            'off_session' => true,
                            'confirm' => true,
                        ]);
                    } catch (\Stripe\Exception\CardException $e) {
                        // Error code will be authentication_required if authentication is needed
                        echo 'Error code is:' . $e->getError()->code;
                        $payment_intent_id = $e->getError()->payment_intent->id;
                        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                        $notify[] = ['error', 'Failed to charge'];
                        return redirect()->route('advertiser.payments')->withNotify($notify);
                    }
                }

                $expectedDateTime->hour(10);
                $expectedDateTime->minute(0);
                $expectedDateTime->second(0);
                $totalMinutes = \Carbon\Carbon::parse($currentDateTime)->diffInMinutes($expectedDateTime);
                if ($totalMinutes < 15) {
                    $expectedDateTime->hour(10);
                    $expectedDateTime->minute(0);
                    $expectedDateTime->second(0);
                } else {
                    $expectedDateTime = \Carbon\Carbon::parse($currentDateTime);
                }


                $transaction = new TransactionAdvertiser();
                $transaction->user_id =  $user->id;
                $transaction->trx_date = $expectedDateTime->Format("Y-m-d h:i:s");
                $transaction->init_blance = getAmount($previous_deposit);
                $transaction->total_budget = getAmount($user->total_budget);
                $transaction->spent_previous_day = getAmount($user->amount_used);
                $deduct_amount = $deducting_amount <= 0 ? 0 : $deducting_amount . '(' . $amount . '+3% service charge)';
                $transaction->deduct = $deduct_amount;
                $transaction->final_wallet =  $user->wallet_deposit;
                $transaction->save();

                $user->nextbill = date('Y-m-d', strtotime(' +1 day',strtotime ( $currentDateTime))) ;  
                $user->save();
            }
        }


        return redirect()->route('advertiser.payments')->withNotify($notify);
    }

    public function charge_current(Request $request)
    {

        $user = Auth::guard('advertiser')->user();
        $currentDateTime = $this->TimezoneFromName($user->country);
        $previous_deposit = $user->wallet_deposit;
        $new_deposit =  $previous_deposit - $user->amount_used;
        $amount =  $user->total_budget - $new_deposit;
        if ($amount > 0) {
            $deducting_amount = $amount + ($amount * 0.03);
            $user->wallet_deposit = $new_deposit +  $amount;
        } else {
            $deducting_amount = 0;
            $user->wallet_deposit = $new_deposit;
        }

        $method = Gateway::where('alias', 'stripe')->firstOrFail();
        $gateway_parameter = json_decode($method->parameters);
        $stripe = new   \Stripe\StripeClient($gateway_parameter->secret_key->value);

        try {
            $setup_intent = $stripe->setupIntents->retrieve($user->card_session, []);
        } catch (\Stripe\Exception\InvalidArgumentException $e) {
            $notify[] = ['error', 'Please add your card'];
            return redirect()->route('advertiser.payments')->withNotify($notify);
        }


        $payment_method_id = $setup_intent->payment_method;
        $payment_method =  $stripe->paymentMethods->retrieve(
            $payment_method_id,
            []
        );
        $customer_id = $payment_method->customer;
        $notify[] = ['success', 'Successfully charged ' . $deducting_amount];
        if ($deducting_amount >= 1) {
            try {
                \Stripe\Stripe::setApiKey($gateway_parameter->secret_key->value);
                \Stripe\PaymentIntent::create([
                    'amount' => $deducting_amount * 100,
                    'currency' => 'usd',
                    'customer' => $customer_id,
                    'payment_method' => $payment_method_id,
                    'off_session' => true,
                    'confirm' => true,
                ]);
            } catch (\Stripe\Exception\CardException $e) {
                // Error code will be authentication_required if authentication is needed
                echo 'Error code is:' . $e->getError()->code;
                $payment_intent_id = $e->getError()->payment_intent->id;
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $notify[] = ['error', 'Failed to charge'];
                return redirect()->route('advertiser.payments')->withNotify($notify);
            }
        }

        $transaction = new TransactionAdvertiser();
        $transaction->user_id =  $user->id;
        $transaction->trx_date = $currentDateTime;
        $transaction->init_blance = getAmount($previous_deposit);
        $transaction->total_budget = getAmount($user->total_budget);
        $transaction->spent_previous_day = getAmount($user->amount_used);
        $deduct_amount = $deducting_amount <= 0 ? 0 : $deducting_amount . '(' . $amount . '+3% service charge)';
        $transaction->deduct = $deduct_amount;
        $transaction->final_wallet =  $user->wallet_deposit;
        $transaction->save();
        // $user->nextbill = date('Y-m-d', strtotime(' +1 day',strtotime ( $currentDateTime))) ;  
        $user->save();

        return redirect()->route('advertiser.payments')->withNotify($notify);
    }

    public function manual_pay(Request $request)
    {

        $user = Auth::guard('advertiser')->user();
        $currentDateTime = $this->TimezoneFromName($user->country);
        $previous_deposit = $user->wallet_deposit;
        $new_deposit =  $previous_deposit + $request->amount;
        $user->wallet_deposit = $new_deposit;

        $method = Gateway::where('alias', 'stripe')->firstOrFail();
        $gateway_parameter = json_decode($method->parameters);
        $stripe = new   \Stripe\StripeClient($gateway_parameter->secret_key->value);

        try {
            $setup_intent = $stripe->setupIntents->retrieve($user->card_session, []);
        } catch (\Stripe\Exception\InvalidArgumentException $e) {
            $notify[] = ['error', 'Please add your card'];
            return redirect()->route('advertiser.payments')->withNotify($notify);
        }


        $payment_method_id = $setup_intent->payment_method;
        $payment_method =  $stripe->paymentMethods->retrieve(
            $payment_method_id,
            []
        );
        $customer_id = $payment_method->customer;
      
        if ( $request->amount >= 1) {
            try {
                \Stripe\Stripe::setApiKey($gateway_parameter->secret_key->value);
                \Stripe\PaymentIntent::create([
                    'amount' =>  $request->amount * 100,
                    'currency' => 'usd',
                    'customer' => $customer_id,
                    'payment_method' => $payment_method_id,
                    'off_session' => true,
                    'confirm' => true,
                ]);
            } catch (\Stripe\Exception\CardException $e) {
                // Error code will be authentication_required if authentication is needed
                echo 'Error code is:' . $e->getError()->code;
                $payment_intent_id = $e->getError()->payment_intent->id;
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $notify[] = ['error', 'Failed to charge'];
                return redirect()->route('advertiser.payments')->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Success!' .  $request->amount];
        $transaction = new TransactionAdvertiser();
        $transaction->user_id =  $user->id;
        $transaction->trx_date = $currentDateTime;
        $transaction->init_blance = getAmount($previous_deposit);
        $transaction->total_budget = "NA";
        $transaction->spent_previous_day = "NA";
        $deduct_amount =  $request->amount <= 0 ? 0 :  $request->amount . '(' .  $request->amount . '+3% service charge)';
        $transaction->deduct = $deduct_amount;
        $transaction->final_wallet =  $user->wallet_deposit;
        $transaction->save();
        // $user->nextbill = date('Y-m-d', strtotime(' +1 day',strtotime ( $currentDateTime))) ;  
        $user->save();

        return redirect()->route('advertiser.payments')->withNotify($notify);
    }

}
