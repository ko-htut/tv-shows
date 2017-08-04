<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Validator;
use Redirect;

class CommentsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $messages = [
            'user_id.required' => 'Pro napsaní komentáře se musíte přihlásit.',
            'content.required' => 'Vyplňte komentář.',
            'content.max' => 'Komentář může obsahovat maximálně 500 znaků.',        
        ];

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required|numeric',
                    'content' => 'required|max:500',
                    'model_id' => 'required|numeric',
                    'model_type' => 'required',
                    'lang' => 'required|min:2|max:2',
                        ], $messages);

        if (!$validator->fails()) {
            $inputs = $request->all();
            $comment = Comment::create($inputs);
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment) {
        $comment->delete();
        return Redirect::back();
    }

   
}
