<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\UserOpeningBalance;

class UserController extends Controller
{
    public function index()
    {

        $users = User::with('branch')->get();
        $allRoles  = Role::all();
        $branches  = \App\Models\Branch::all();
        
        // attach opening balance for today
        foreach($users as $user) {
            $user->today_opening = UserOpeningBalance::where('user_id', $user->id)
                ->where('date', date('Y-m-d'))
                ->first();
        }

        return view('admin_panel.users.users', compact(['users', 'allRoles', 'branches']));
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $request->edit_id,
            'password' => $editId ? 'nullable' : 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Step 3: Save or update logic
        if (!empty($editId)) {
            $user = User::find($editId);
            $msg = [
                'success' => 'User Updated Successfully',
                'reload' => true
            ];
        } else {
            $user = new User();
            $msg = [
                'success' => 'User Created Successfully',
                'redirect' => route('users.index')
            ];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->filled('branch_id')) {
            $user->branch_id = $request->branch_id;
        }
        $user->save();

        return response()->json($msg);
    }

    /**
     * Display the specified resource.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateRoles(Request $request)
    {
        $user = User::findOrFail($request->edit_id);

        // Assign new roles (by name)
        $user->syncRoles($request->roles ?? []);

        return back()->with('success', 'User roles updated successfully!');
    }

    public function storeOpeningBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        UserOpeningBalance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => $request->date
            ],
            [
                'amount' => $request->amount,
                'note' => $request->note,
                'created_by' => auth()->id()
            ]
        );

        return back()->with('success', 'Opening Balance updated successfully!');
    }
}
