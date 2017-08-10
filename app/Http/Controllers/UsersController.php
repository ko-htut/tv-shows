<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use Illuminate\Http\Request;
use Validator;
use Redirect;
use Symfony\Component\Console\Input\Input;

class UsersController extends LayoutController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::all();
        //dd($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //in registration
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        return view('users.detail', compact(['user']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        return view('users.edit', compact(['user']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {

        if ($request->get('uId') != \Auth::user()->id) {
            return Redirect::back();
        }

        $validator = Validator::make($request->all(), [
                    'username' => 'required|unique:users,username,' . $user->id . '|min:2|max:80',
                    'email' => 'required|email|unique:users,email,' . $user->id . '|min:3|max:80',
                    'first_name' => 'nullable|max:255',
                    'last_name' => 'nullable|max:255',
                    'gender' => 'nullable|max:1',
                    'birthday' => 'nullable|date',
                    'about' => 'nullable|max:500',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);



        if (!$validator->fails()) {
            $u = User::find($user->id);
            $u->username = $request->get('username');
            $u->email = $request->get('email');
            $u->first_name = $request->get('first_name');
            $u->last_name = $request->get('last_name');
            $u->gender = $request->get('gender');
            $u->birthday = $request->get('birthday');
            $u->about = $request->get('about');
            $u->save();

            $avatar = $request->file('avatar');
            if ($avatar) {
                $patch = 'public/img/users/'; //storage dir
                $filename = 'avatar-' . $user->id . '.' . $avatar->getClientOriginalExtension();
                $avatar->storeAs($patch, $filename);
                $arr = [
                    'type' => 'avatar',
                    'patch' => '/storage/app/' . $patch . $filename, //+add prefix
                    'extension' => $avatar->getClientOriginalExtension(),
                    'file_size' => $avatar->getClientSize(),
                    'model_id' => $user->id,
                    'model_type' => $user->type,
                ];
                File::updateOrCreate(
                        ['model_id' => $user->id, 'model_type' => $user->type, 'type' => 'avatar'], $arr
                );
                //::TODO Need to delete files if diferent extension at disk.
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $user->delete();
        return Redirect::back();
    }

}
