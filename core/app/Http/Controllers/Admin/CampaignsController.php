<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;

use App\Advertiser;
use App\campaigns;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeadsExport;
use App\Imports\LeadsImport;
use Carbon\Carbon;


class CampaignsController extends Controller
{
    public function index()
    {
        $page_title = 'All Campaigns';
        $empty_message = 'No Campaigns';
        
        $campaigns = campaigns::with('advertiser')->with('campaign_forms')->where('status', '!=', 2);
        $pending = campaigns::with('advertiser')->with('campaign_forms')->where('status', 2)->where('approve', 0);
        $activeDelivery = campaigns::with('advertiser')->with('campaign_forms')->where('approve', 1)->where('status', 1)->where('delivery',1);
        $active = campaigns::with('advertiser')->with('campaign_forms')->where('approve', 1)->where('status', 1)->where('delivery',0);
        $reject = campaigns::with('advertiser')->with('campaign_forms')->where('approve', 2)->where('status', 3);
        $trash = campaigns::with('advertiser')->with('campaign_forms')->where('status', 4)->orWhere('status',5);


        if (isset($_GET['advertiser'])){
            $advertiserid = $_GET['advertiser'];
           $active->where('campaigns.advertiser_id','=',$advertiserid);
           $activeDelivery->where('campaigns.advertiser_id','=',$advertiserid);
           $campaigns->where('campaigns.advertiser_id','=',$advertiserid);
           $pending->where('campaigns.advertiser_id','=',$advertiserid);
           $reject->where('campaigns.advertiser_id','=',$advertiserid);
           $trash->where('campaigns.advertiser_id','=',$advertiserid);
        }
        $active = $active->orderBy('id', 'DESC')->get();
        $activeDelivery = $activeDelivery->orderBy('id', 'DESC')->get();
        $campaigns = $campaigns->orderBy('id', 'DESC')->get();
        $pending = $pending->orderBy('id', 'DESC')->get();
        $reject = $reject->orderBy('id', 'DESC')->get();
        $trash = $trash->orderBy('id', 'DESC')->get();
        $companies = DB::table('campaigns')
        ->leftJoin('advertisers', 'campaigns.advertiser_id', '=', 'advertisers.id')
        ->where('advertisers.company_name','!=','')
        ->groupBy('advertisers.company_name')
        ->get();
        

        return view('admin.campaigns.index', compact('page_title', 'empty_message', 'campaigns','companies', 'pending', 'active', 'activeDelivery','reject', 'trash'));
    }

    public function export($cid, $aid, $fid)
    {
        $campaign_id = $cid;
        $advertiser_id = $aid;
        $campaign = campaigns::where('id', $campaign_id)->with('campaign_forms')->first();
        if ($campaign) {
            $campaign_name =  $campaign['name'];
            $campaign_form = $campaign['campaign_forms'];
            return Excel::download(new LeadsExport($campaign_id, $advertiser_id, $campaign_name, $campaign_form), 'leads.xlsx');
        }
    }

    public function import(Request $request, $cid, $aid, $fid)
    {
        $request->validate(['file' => 'required|mimes:xlsx, xls']);
        $campaign_id = $cid;
        $advertiser_id = $aid;
        $form_id = $fid;
        $LeadsImportReturn = new LeadsImport($campaign_id, $advertiser_id, $form_id, false);
        $LeadsImportReturn->import($request->file('file')->store('files'));
        $LeadsValidationErrors = $LeadsImportReturn->getErrors();
        $LeadsData = $LeadsImportReturn->getLeadsData();
        if ($LeadsData) {
            return response()->json(['success' => true, 'data' => $LeadsData]);
        } else {
            return response()->json(['success' => false, 'data' => $LeadsValidationErrors]);
        }
    }

    public function importPreview(Request $request, $cid, $aid, $fid)
    {
        $request->validate(['file' => 'required|mimes:xlsx, xls']);
        $campaign_id = $cid;
        $advertiser_id = $aid;
        $form_id = $fid;
        $LeadsImportReturn = new LeadsImport($campaign_id, $advertiser_id, $form_id, true);
        $LeadsImportReturn->import($request->file('file')->store('files'));
        // getErrors on LeadsImportReturn class:
        $LeadsValidationErrors = $LeadsImportReturn->getErrors();
        $LeadsData = $LeadsImportReturn->getLeadsData();
        if ($LeadsData) {
            return response()->json(['success' => true, 'data' => $LeadsData]);
        } else {
            return response()->json(['success' => false, 'data' => $LeadsValidationErrors]);
        }
    }
    public function update_approval(Request $request)
    {
        $request->validate(['approval' => 'required', 'campaign_id' => 'required']);
        $campaign = campaigns::findOrFail($request->campaign_id);
        if ($campaign) {
            $campaign->approve =  $request->approval;
            $campaign->delivery = $request->approval;
            $campaign->start_date = Carbon::now();
            $campaign->update();
            if ($request->approval  == 1) {
                $user = Advertiser::select('name', 'email')->findOrFail($campaign->advertiser_id);
                $data = array(
                    'campaign_name' => $campaign->name,
                    'advertiser_name' => $user->name,
                    'advertiser_email' => $user->email,
                    'campaign_url' =>  route('advertiser.campaigns.index')
                );
                send_email_campaign_approval($user, 'EVER_CODE', $data);
                return response()->json(['success' => true, 'message' => 'Campaign successfully approve']);
            } else {
                return response()->json(['success' => false, 'message' => 'Campaign successfully unapprove']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Somthing Worng please try again']);
        }
    }

    public function update_approval_rejection(Request $request)
    {
        $request->validate(['approval' => 'required', 'campaign_id' => 'required', 'remarks' => 'required']);
        $campaign = campaigns::findOrFail($request->campaign_id);
        if ($campaign) {
            $campaign->approve =  $request->approval;
            $campaign->rejection_remarks =  $request->remarks;
            $campaign->delivery = $request->approval;
            $campaign->start_date = Carbon::now();
            $campaign->update();
            return response()->json(['success' => false, 'message' => 'Campaign successfully unapprove']);
        } else {
            return response()->json(['success' => false, 'message' => 'Somthing Worng please try again']);
        }
    }


    public function campaign_delete($id)
    {
        campaigns::where('id', $id)->update(array('status' => 2));
        $notify[] = ['success', 'Campaign banned successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function campaign_complete_delete($id)
    {
        campaigns::where('id', $id)->delete();
        $notify[] = ['success', 'Campaign deleted successfully'];
        return redirect()->back()->withNotify($notify);
    }
    public function campaign_restore($id)
    {
        campaigns::where('id', $id)->update(array('status' => 1));
        $notify[] = ['success', 'Campaign Restore Successfully'];
        return redirect()->back()->withNotify($notify);
    }
}
