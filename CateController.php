<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CateRequest;
use Illuminate\Http\Request;


class CateController extends Controller
{

    public function getList()
    {
        $data = Category::select('id', 'name', 'parent_id')->orderBy('id', 'DESC')->get()->toArray();
        return view('admin.cate.list', compact('data'));
    }

    public function getAdd()
    {
        $parent = Category::select('id', 'name', 'parent_id')->get()->toArray();
        return view('admin.cate.add', compact('parent'));
    }

    public function postAdd(CateRequest $request)
    {
        $cate              = new Category;
        $cate->name        = $request->txtCateName;
        $cate->alias       = changeTitle($request->txtCateName);
        $cate->order       = $request->txtCateOrder;
        $cate->parent_id   = $request->slCateParent;
        $cate->keywords    = $request->txtCateKeywords;
        $cate->description = $request->txtCateDescription;
        $cate->save();
        return redirect()->route('admin.cate.list')->with(['mess_content' => 'Insert Cate Complete!!', 'mess_level' => 'success']);
    }

    public function getEdit($id)
    {
        $parent = Category::select('id', 'name', 'parent_id')->get()->toArray();
        $obj    = Category::findOrFail($id)->toArray();
        return view('admin.cate.edit', compact('obj', 'parent'));
    }

    public function postEdit($id, Request $request){
        $this->validate($request, 
            ["txtCateName" => "required"],
            ["txtCateName.required" => "Please Enter Category Name!!"]
        );
        $obj              = Category::find($id);
        $obj->name        = $request->txtCateName;
        $obj->alias       = changeTitle($request->txtCateName);
        $obj->parent_id   = $request->slCateParent;
        $obj->order       = $request->txtCateOrder;
        $obj->keywords    = $request->txtCateKeywords;
        $obj->description = $request->txtCateDescription;
        $obj->save();
        return redirect()->route('admin.cate.list')->with(['mess_content' => 'Update Cate Complete!!', 'mess_level' => 'success']);
    }

    public function getDelete($id)
    {
        $nParent = Category::where('parent_id', $id)->count();
        if ($nParent == 0) {
            $data = Category::findOrFail($id);
            $data->delete();
        } else {
            return redirect()->route('admin.cate.list')->with(['mess_content' => 'Can not delete this object!!', 'mess_level' => 'warning']);
        }
        return redirect()->route('admin.cate.list')->with(['mess_content' => 'Delete Complete!!', 'mess_level' => 'success']);
    }


}
