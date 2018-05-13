<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\MonHoc;
use App\Http\Requests\MonHocRequest;

class MyController extends Controller
{
    //Khởi tạo 1 function trong Controller
    public function HelloWorld(){
    	echo "<h1>This is my Controller</h1>";
    }

    //Truyền dữ liệu từ Route sang Controller 
    public function ReceiveData($a){
    	echo "Data receive is: <strong>".$a."</strong>";
    	return redirect()->route('MyRoute-2');
    }

    public function getData2(Request $request)
    {
    	return $request->path();
    }

    public function something(Request $request){
    	echo $request->lastName;
    	/**
    	 * Biến request lưu các thông tin gởi từ Form lên
    	 * Để lấy được dữ liệu thì gọi biến $request->name đối tượng
    	 */
    }

    //----------------------------------------------
    //Hàm getForm hiển thị nội dung từ file FormRequest.blade.php
    //-> view('FormRequest'); Hiển thị nội dung file
    public function getForm()
    {
    	return view('FormRequest');	
    }

    //Trả về nội dung biến Request['name']
    public function handleRequest(Request $request)
    {
    	return $request->name;
    }

    public function getView(){
    	return view('postForm');
    }

    public function getData(Request $request)
    {
    	return $request->name;
    }

    public function showView(){
    	return view('userInfoForm');
    }

    public function getDataFromView(Request $request)
    {
    	$result = "";
    	if($request->has('hoTen') && $request->has('tuoi')){
    		$name = $request->hoTen;
    		$age  = $request->input('tuoi');
    		$result = "This is ".$name.". He is ".$age." years old.";
    	}
    	else {
    		echo "Không có tham số!!";
    		return false;
    	}
    	return $result;
    }

    public function setCookie()
    {
    	$response = new Response; //Khởi tạo biến chứa cookie
    	$response->withCookie(
    		'name', //Tên cookie
    		'This is setCookie function. <br>You only get data after you run setCookie function.', //Giá trị của cookie
    		1 //Thời gian tồn tại của cookie
    	);
    	return $response;
    }

    public function getCookie(Request $request)
    {
    	return $request->cookie('name');    	
    }

    public function getFileForm(){
    	return view('fileForm');
    }

    //Hàm xử lý file
    public function getFileUpload(Request $request)
    {
    	if($request->hasFile('file-upload')){
    		$fileUpload = $request->file('file-upload');
    		echo "Đã có file upload <hr> <strong>THÔNG TIN FILE ĐÃ UPLOAD</strong>";
    		echo "<br/>Tên file upload: ".$fileUpload->getClientOriginalName();
    		echo "<br/>Phần mở rộng của file: ".$fileUpload->getClientOriginalExtension();
    		echo "<br/>Dung lượng file: ".$fileUpload->getClientSize();
    		echo "<br/>Loại file: ".$fileUpload->getClientMimeType();
            echo "<br/>Đường dẫn file:".$fileUpload->getRealPath();
    	}
    	else {
    		echo "Chưa có file upload";
    	}
    }

    public function showFormView($viewName)
    {
    	return view("$viewName");
    }

    public function getUserData(Request $request)
    {
    	$ex_arr = array('jpg', 'jpeg', 'png');
    	$name = $request->input('name');
    	$age  = $request->input('age');
    	if($request->hasFile('file-upload')){
    		$file = $request->file('file-upload');
    		$fileName = $file->getClientOriginalName();
    		$fileSize = $file->getClientSize();
    		$fileType = $file->getClientMimeType();
    		$fileEx	  = $file->getClientOriginalExtension();
    		$file->move('images', $name);
    		if(in_array($fileEx, $ex_arr) == false){
    			return "File upload is not image file";
    		}
    		if($file->isValid()){
    			return "Fail to upload file";
    		}
    	}
    	else {
    		return "File upload does not exists";
    	}
    	echo "<strong>USER'S INFORMATION</strong><hr>";
    	echo "<br/>Name: ".$name;
    	echo "<br/>Age: ".$age;
    	echo "<hr>FILE UPLOAD'S INFORMATION<hr>";
    	echo "<br/>Tên file upload: ".$fileName;
		echo "<br/>Phần mở rộng của file: ".$fileEx;
		echo "<br/>Dung lượng file: ".$fileSize;
		echo "<br/>Loại file: ".$fileType;
    }

    //Hàm gởi dữ liệu JSON
    //Hàm truyền mảng theo Json
    public function getArrWithJson()
    {
    	$data = array(
    		"sv1" => [
    					"name"=>"Warrent Buffet",
    					"age" => 20
    				 ],
    		"sv2" => [
    					"name"=>"Bill Gates",
    					"age" => 15
    				 ]
    	);
    	return response()->json($data);
    	
    }


    public function getJson()
    {
    	return response()->json([
    		"sv1"=> [
    			"name"=> "Micheal Carrick",
    			"age"=> 2
    		],
    		"sv2"=> [
    			"name"=> "Anthony Martial",
    			"age"=> 3
    		]
    	]);
    }
    //--------------------------------------------
    /*
     *Truyền tham số cho View không thông qua URL
     */
    public function showDataFromRoute(){
    	$name = "Warrent Buffet";
		$job  = "Business";
    	//compact('tên biến');
		echo view('getDataFromRoute', compact('name', 'job'));
		return redirect()->route('MyRoute-2');
    }

    /**
     * Test layout if else
     */
    public function getInfoFromUser(Request $request){
        $name = $request->input('name');
        $age  = $request->input('age');
        $sex  = $request->sex;
        $table = $request->table;
        if($table == 1){
            $row = $request->input('row');
            $collum = $request->input('collum');
            return view('layouts.showSomethingForm', compact(['name', 'age', 'sex', 'table', 'row', 'collum']));
        }
        return view('layouts.showSomethingForm', compact(['name', 'age', 'sex']));
    }

    public function showMenu()
    {
        return view('menu');
    }

    public function CourseReg(MonHocRequest $request){
        $fileUpload = $request->file('image');
        $fileName = $fileUpload->getClientOriginalName();
        $fileUrl = $fileUpload->getRealPath();
        $des = 'images';
        $fileUpload->move($des, $fileName);
        $monhoc = new MonHoc; 
        $monhoc->name  = $request->name;
        $monhoc->teacher  = $request->teacher;
        $monhoc->price  = $request->price;
        $monhoc->image = $fileName;
        $monhoc->urlImage = $fileUrl;
        $monhoc->save();
        return "<script>alert('Insert complete!!');history.back();</script>";
    }
}

