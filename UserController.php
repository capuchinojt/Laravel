<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function getAdd()
    {
        return view('admin.user.add');
    }

    public function getList()
    {
        $users = User::select()->orderBy('id', 'DESC')->get()->toArray();
        return view('admin.user.list', compact('users'));
    }

    public function postAdd(UserRequest $request)
    {
        $user                 = new User;
        $user->username       = $request->txtUser;
        $user->password       = Hash::make($request->txtPass);
        $user->email          = $request->txtEmail;
        $user->level          = (int) $request->radLevel;
        $user->remember_token = $request->_token;
        $user->save();
        return redirect()->route('admin.user.list')->with(['mess_content' => 'Insert User Complete!!', 'mess_level' => 'success']);
    }

    public function delete($id){
        $user_cur_login = Auth::user()->name;
        $user_del = User::find($id);
        if(($user_cur_login == $id) || ($id == 5) || ($user_del->level == 1)){
            return redirect()->route('admin.user.list')->with(['mess_content' => 'Access Is Denied!! Do Not Complete This Action!!', 'mess_level' => 'danger']);
        }
        else {
            $user_del->delete();
            return redirect()->route('admin.user.list')->with(['mess_content' => 'Delete User Complete!!', 'mess_level' => 'success']);
        }
    }

    public function getEdit($id)
    {
        $user = User::find($id)->toArray();
        if (Auth::user()->username != 'admin' && (($user['username'] == 'admin') ||  ($user['level'] == 1 && Auth::user()->id != $user['id']))) {
            return redirect()->route('admin.user.list')->with(['mess_content' => 'Access Denied!! You Are Not SuperAdmin To Do This!!', 'mess_level' => 'danger']);
        } else {
            return view('admin.user.edit', compact('user', 'id'));
        }
    }

    public function postEdit($id, Request $request)
    {
        $user = User::find($id);
        if ($request->input('txtPass')) {
            $this->validate($request,
                [
                    'txtRePass' => 'same:txtPass',
                ],
                [
                    'txtRePass.same' => 'Retype-Password and Password Do Not Match',
                ]
            );
            $user->password = Hash::make($request->input('txtPass'));
        }
        $user->email          = $request->input('email');
        $user->level          = $request->input('radLevel');
        $user->remember_token = $request->input('_token');
        $user->save();
        return redirect()->route('admin.user.list')->with(['mess_content' => 'Update User Info Complete!!', 'mess_level' => 'success']);

    }
}
