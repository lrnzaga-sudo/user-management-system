<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function home() {
        return view('users.home');
    }

    public function currentUser(Request $request) {
        return response()->json([
            'user' => new UserResource($request->user())
        ], 200);
    }

    public function editPage() {
        return view('users.edit');
    }

    public function editInfo(UserEditRequest $request) {
        $user = $request->user();

        $data = ['username' => $request->username];
        $data['password'] = Hash::make($request->password);

        $user->update($data);

        return response()->json([
            'message' => 'Edit successfully',
            'user' => new UserResource($user)
        ], 200);
    }
}
