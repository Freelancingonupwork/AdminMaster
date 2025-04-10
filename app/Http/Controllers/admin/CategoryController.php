<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(){
        $categoryData = Category::with('parentCategory')->get()->toArray();
        // dd($categoryData);
        return view('admin.category.index')->with(compact('categoryData'));
    }

    public function create(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

            $validation = [
                'category_name' => ['required','string', 'nullable', 'max:255'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if(empty($data['parent_id'])){
                $parentId = 0;
            }else{
                $parentId = $data['parent_id'];
            }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $slug = Str::slug($data['category_name']);

            $category = new Category;
            $category->category_name = $data['category_name'];
            $category->parent_id = $parentId;
            $category->description = $data['description'];
            $category->slug = $slug;
            $category->status = $status;
            $category->save();
            return redirect('/admin/category-index')->with('success','Category Inserted Successfully !!!');
        }
        $levels = Category::with('subCategory')->where(['parent_id'=> 0])->get();
        return view('admin.category.create')->with(compact('levels'));
    }

    public function update(Request $request, $slug, $id){
        if($request->isMethod('post')){
            $data = $request->all();

            $validation = [
                'category_name' => ['required','string', 'nullable', 'max:255'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if(empty($data['parent_id'])){
                $parentId = 0;
            }else{
                $parentId = $data['parent_id'];
            }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $slug = Str::slug($data['category_name']);

            $updateCategory = Category::where(['id' => $id])->update(['category_name' => $data['category_name'], 'slug' => $slug, 'parent_id' => $parentId, 'description' => $data['description'], 'status' => $status]);
            return redirect('/admin/category-index')->with('success','Category updated successfully !!!');
        }

        $category = Category::where(['slug' => $slug, 'id' => $id])->first();

        $levels = Category::with('subCategory')->where(['parent_id'=> 0])->get();
        return view('admin.category.create')->with(compact('category','levels'));
    }

    public function destroy($slug, $id){
        Category::where(['slug' => $slug, 'id' => $id])->delete();
        return redirect()->back();
    }
}
