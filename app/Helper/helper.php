<?php

namespace App\Helper;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class Helper
{
    public static function plans(){
        $planData = Plan::where(['status' => 1])->get()->toArray();
        return $planData;
    }

    public static function userSubscription(){
        $userSubscriptionData = "";
        if(Auth::check()){
            $userSubscriptionData = Subscription::where(['user_id' => Auth::user()->id, 'subscription_status' => 1, 'status' => 1])->first();
        }
        return $userSubscriptionData;
    }
}