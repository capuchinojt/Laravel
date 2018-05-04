<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\ProductImage;
use File;
use Illuminate\Support\Facades\Input;
use Request;
use Auth;

class ProductController extends Controller
{
    public function getList()
    {
        $data = Product::select()->orderBy('id', 'DESC')->get()->toArray();
        return view('admin.product.list', compact('data'));
    }

    public function getAdd()
    {
        $cate = Category::select('id', 'name', 'parent_id')->get()->toArray();
        return view('admin.product.add', compact('cate'));
    }

    public function postAdd(ProductRequest $request)
    {
        $product    = new Product;
        $fileupload = $request->file('fileupload');
        $fileName   = time() . "-" . $fileupload->getClientOriginalName();
        $fileupload->storeAs('/upload/images', $fileName);
        $product->name        = $request->txtName;
        $product->alias       = changeTitle($request->txtName);
        $product->price       = $request->txtPrice;
        $product->intro       = $request->txtIntro;
        $product->content     = $request->txtContent;
        $product->keywords    = $request->txtKeywords;
        $product->description = $request->txtDescription;
        $product->image       = $fileName;
        $product->cate_id     = $request->slCate;
        $product->user_id     = Auth::user()->id;
        $product->save();
        $product_id = $product->id;
        if (Input::hasFile('imgDetail')) {
            foreach ($request->imgDetail as $imgFile) {
                $pro_img = new ProductImage();
                if (isset($imgFile)) {
                    $imgName        = time() . "-" . $imgFile->getClientOriginalName();
                    $pro_img->image = $imgName;
                    $imgFile->storeAs('/upload/images-detail', $imgName);
                    $pro_img->product_id = $product_id;
                    $pro_img->save();
                }
            }
        }
        return redirect()->route('admin.pro.list')->with(['mess_content' => 'Insert Product Complete!!', 'mess_level' => 'success']);
    }

    public function getDelete($id)
    {
        $img = Product::find($id)->images->toArray();
        foreach ($img as $value) {
            File::delete('storage/app/upload/images-detail/' . $value['image']);
        }
        $product = Product::find($id);
        File::delete('storage/app/upload/images/' . $product->image);
        $product->delete();
        return redirect()->route('admin.pro.list')->with(['mess_content' => 'Delete Product Complete!!', 'mess_level' => 'success']);
    }

    public function getEdit($id)
    {
        $idP      = $id;
        $cate     = Category::select()->get()->toArray();
        $obj      = Product::find($id)->toArray();
        $prod_img = Product::find($id)->images->toArray();
        $imgCount = Product::find($id)->images->count();
        return view('admin.product.edit', compact('cate', 'obj', 'idP', 'prod_img', 'imgCount'));
    }

    public function postEdit($id, Request $request)
    {
        $product              = Product::find($id);
        $product->name        = Request::input('txtName');
        $product->alias       = changeTitle(Request::input('txtName'));
        $product->price       = Request::input('txtPrice');
        $product->intro       = Request::input('txtIntro');
        $product->content     = Request::input('txtContent');
        $product->keywords    = Request::input('txtKeywords');
        $product->description = Request::input('txtDescription');
        $product->cate_id     = Request::input('slCate');
        $product->user_id     = Auth::user()->id;;
        $product_id           = $product->id;
        if (!empty(Request::file('fileupload'))) {
            $fileName       = Request::file('fileupload')->getClientOriginalName();
            $product->image = time() . '-' . $fileName;
            $exImg          = 'storage/app/upload/images/' . Request::input('img_current');
            if (File::exists($exImg)) {
                File::delete($exImg);
            }
            Request::file('fileupload')->storeAs('upload/images', time() . '-' . $fileName);
        }
        if (!empty(Request::file('imgDetail'))) {
            $pro_img = new ProductImage;
            foreach (Request::file('imgDetail') as $img) {
                $img_name            = time() . '-' . $img->getClientOriginalName();
                $pro_img->image      = $img_name;
                $pro_img->product_id = $product_id;
                $img->storeAs('upload/images-detail/', $img_name);
                $pro_img->save();
            }
        }
        $product->save();
        return redirect()->route('admin.pro.list')->with(['mess_content' => 'Update Product Complete!!', 'mess_level' => 'success']);
    }

    public function getDelImg($id)
    {
        if (Request::ajax()) {
            $idImg        = (int) Request::get('idImg');
            $image_detail = ProductImage::find($idImg);
            if (!empty($image_detail)) {
                $img = "storage/app/upload/images-detail/" . $image_detail->image;
                if (File::exists($img)) {
                    File::delete($img);
                }
                $image_detail->delete();
            }
            return "OK";
        }
    }
}
