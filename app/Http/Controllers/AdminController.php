<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
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
    public function store(Request $request)
    {
        DB::table('users')->insert([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);
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

    public function home() {
        return view('admin.home');
    }

    public function users() {
        $users = User::userRole()
                    ->paginate(10);

        return response()->json([
            'users' => UserResource::collection($users)
        ], 200);
    }

    public function addUserPage() {
        return view('admin.addUser');
    }

    public function addUser(StoreUserRequest $request) {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'message' => 'User added successfully',
            'user' => new UserResource($user)
        ], 201);
    }

    public function search(Request $request) {
        $search = $request->search;

        if (!$search) {
            return response()->json([
                'users' => UserResource::collection(
                    User::userRole()->get()
                )
            ], 200);
        }

        $users = User::where('id', $search)
                ->orWhere('username', 'like', '%' . $search . '%')
                ->userRole()
                ->get();

        return response()->json([
            'users' => UserResource::collection($users)
        ], 200);
    }

    public function getUser($id)
    {
        $user = User::where('id', $id)
                ->userRole()
                ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }

    // public function viewEditUser(string $id) {
    //     $user = User::where('id', $id)
    //             ->where('role', 'user')
    //             ->first();
        
    //     return response()->json([
    //         'user' => new UserResource($user)
    //     ], 200);
    // }

    public function editUser(EditUserRequest $request, $id) {
        $user = User::where('id', $id)
                ->userRole()
                ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = ['username' => $request->username];
        $data['password'] = Hash::make($request->password);

        $user->update($data);

        return response()->json([
            'message' => 'Edit successfully',
            'user' => new UserResource($user)
        ], 200);
    }

    public function deleteUser($id) {
        $user = User::where('id', $id)
                ->userRole()
                ->first();
            
        $user->delete();

        return response()->json([
            'message' => 'Delete Successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);
    //     $this->authorize('update', $user);

    //     User::where('id', $id)
    //         ->update([
    //             'username' => $request->username
    //         ]);
        
    //     return redirect()->route('user_list');
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $userToDelete = User::findOrFail($id);

    //     $this->authorize('delete', $userToDelete);
        
    //     $userToDelete->delete();

    //     return back();
    // }

    // public function register(StoreUserRequest $request) {
    //     $user = User::create([
    //         'username' => $request->username,
    //         'password' => Hash::make($request->password),
    //         'role' => 'admin'
    //     ]);

    //     return response()->json([
    //         'message' => 'Registration Successfull',
    //         'user' => new RegisterResource($user)
    //     ], 201);
    // }

    // public function login(LoginUserRequest $request) {
    //     $admin = User::where('username', $request->username)
    //                 ->where('role', 'admin')
    //                 ->first();
    //     if ($admin != null && Hash::check($request->password, $admin->password)) {

    //         Auth::login($admin);

    //         $request->session()->regenerate();

    //         return redirect()->route('user_list');
    //     }

    //     return back()->with('error', 'Invalid username or password.');
    // }

    // public function logout(Request $request) {

    //     $user = Auth::user();

    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
        
    //     return redirect()->route('admin_login');
    // }

    // public function addUser(StoreUserRequest $request) {
    //     User::create([
    //         'username' => $request->username,
    //         'password' => Hash::make($request->password),
    //         'role' => 'user'
    //     ]);

    //     return redirect()->route('user_list');
    // }

    // public function search(Request $request) {

    //     if (!$request->search) {
    //         return redirect()->route('user_list');
    //     }

    //     $users = User::where('id', $request->search)
    //                 ->where('role', 'user')
    //                 ->paginate(3);
        
    //     if (!$users) {
    //         return back()->with('error', 'No users found');
    //     }
        
    //     return view('admin.users', compact('users'));
    // }

}
