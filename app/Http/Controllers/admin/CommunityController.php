<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\DiscussionReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index()
    {
        $discussionData = Community::get()->toArray();
        return view('admin.community.index', ['discussionData' => $discussionData]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $validation = [
                'topic' => ['required', 'string'],
                'content' => ['required', 'string', 'max:250']
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            $slug = Str::slug($data['topic']);

            $discussion = new Community;
            $discussion->topic = $data['topic'];
            $discussion->content = $data['content'];
            $discussion->slug = $slug;
            $discussion->status = $status;
            $discussion->save();
            return redirect('/admin/discussion-index')->with('success', 'Discussion Inserted Successfully !!!');
        }
        return view('admin.community.create');
    }

    public function update(Request $request, $slug, $id)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // echo "<pre>"; print_r($data); die;
            $validation = [
                'topic' => ['required', 'string'],
                'content' => ['required', 'string', 'max:250']
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            $slug = Str::slug($data['topic']);

            $updateDiscussion = Community::where(['id' => $id])->update(['topic' => $data['topic'], 'slug' => $slug, 'content' => $data['content'], 'status' => $status]);
            return redirect('/admin/discussion-index')->with('success', 'Discussion updated successfully !!!');
        }

        $discussion = Community::where(['slug' => $slug, 'id' => $id])->first();
        return view('admin.community.create')->with(compact('discussion'));
    }

    public function destroy($slug, $id)
    {
        Community::where(['slug' => $slug, 'id' => $id])->delete();
        return redirect()->back()->with('success', "Discussion deleted successfully.");
    }

    public function getDiscussion(Request $request)
    {
        $discussionsAddedBy = User::where(['type' => 'superadmin'])->first();
        $discussionData = Community::get()->toArray();
        $discussionReply = DiscussionReply::with('user')->get()->toArray();
        // dd($discussionReply);
        return view('front.community.discussion')->with(compact('discussionsAddedBy', 'discussionData', 'discussionReply'));
    }

    public function reply(Request $request)
    {
        $data = $request->all();
        $validation = [
            'discussion_reply' => ['required', 'string', 'max:3000'],
        ];
        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
        }

        $discussionReply = new DiscussionReply;
        $discussionReply->user_id = Auth::user()->id;
        $discussionReply->topic_id = $data['topic_id'];
        $discussionReply->discussion_reply = $data['discussion_reply'];
        $discussionReply->save();

        return redirect()->back()->with('success', 'You just replied to a discussion');
    }
}
