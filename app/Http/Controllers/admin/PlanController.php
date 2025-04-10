<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $planData = Plan::get()->toArray();
        return view('admin.plans.index')->with(compact('planData'));
    }

    public function create(Request $request)
    {
        if($request->isMethod('Post')){
            $data = $request->all();

            $validation = [
                'name' => ['required','string', 'nullable', 'max:255'],
                'description' => ['required','string', 'nullable', 'max:355','min:100'],
                'price' => ['required', 'nullable', 'max:255'],

            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $slug = Str::slug($data['name']);

            if (!isset($data['image']) || empty($data['image'])) {
                $planImage = '';
            } else {
                $planImage = time() . '.' . $data['image']->extension();
                if (! Storage::disk('public')->exists("/plans/plan_images")) {
                    Storage::disk('public')->makeDirectory("/plans/plan_images"); //creates directory
                }
                $request->image->storeAs("/plans/plan_images/", $planImage, 'public');

                $planImage = "/plans/plan_images/$planImage";
            }

            $plan = new Plan;
            $plan->name = $data['name'];
            $plan->user_id = Auth::user()->id;
            $plan->price = $data['price'];
            $plan->plan_type = $data['plan_type'];
            $plan->description = $data['description'];
            $plan->slug = $slug;
            $plan->image = $planImage;
            $plan->status = $status;
            $plan->save();
            return redirect('/admin/plan-index')->with('success','Plan inserted successfully !!!');
        }
        return view('admin.plans.create');
    }

    public function update(Request $request, $slug, $id){
        $plan = Plan::where(['id' => $id])->first();
        if($request->isMethod('Post')){
            $data = $request->all();

            $validation = [
                'name' => ['required','string', 'nullable', 'max:255'],
                'description' => ['required','string', 'nullable', 'max:355','min:100'],
                'price' => ['required', 'nullable', 'max:255'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $slug = Str::slug($data['name']);

            if (!isset($data['image']) || empty($data['image'])) {
                $planImage = $plan['image'];
            } else {
                $planImage = time() . '.' . $data['image']->extension();
                if (!Storage::disk('public')->exists("/plans/plan_images")) {
                    Storage::disk('public')->makeDirectory("/plans/plan_images"); //creates directory
                }
                if (Storage::disk('public')->exists("/plans/" . $plan['image'])) {
                    Storage::disk('public')->delete("/plans/" . $plan['image']);
                }
                $request->image->storeAs("plans/plan_images", $planImage, 'public');

                $planImage = "plans/plan_images/$planImage";
            }

            $updatePlan = Plan::where(['id' => $id])->update(['name' => $data['name'], 'description' => $data['description'], 'slug' => $slug, 'price' => $data['price'], 'plan_type' => $data['plan_type'], 'image' => $planImage, 'slug' => $slug, 'status' => $status]);

            return redirect('/admin/plan-index')->with('success', 'Plan updated Successfully !!!');
        }

        return view('admin.plans.create')->with(compact('plan'));
    }

    public function destroy($slug, $id)
    {
        Plan::where(['slug' => $slug, 'id' => $id])->delete();
        return redirect()->back();
    }
}
