<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        $users = User::where('email', '!=' ,'admin@admin.com')->get();
        return view('admin_panel.branch.branch', compact('branches', 'users')); 
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
         $validator = Validator::make($request->all(), [
            'name' => 'required|unique:branches,name,'.$request->edit_id,
            'address' => 'required',
            'number' => 'required',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Step 2: Check for user_id uniqueness (exclude self in edit)
        if ($request->filled('user_id')) {
            $userExists = Branch::where('user_id', $request->user_id)
                ->when($editId, fn($q) => $q->where('id', '!=', $editId))
                ->exists();

            if ($userExists) {
                return response()->json([
                    'errors' => [
                        'user_id' => ['This user is already assigned to another branch.']
                    ]
                ]);
            }
        }

        // Step 3: Save or update logic
        if (!empty($editId)) {
            $branch = Branch::find($editId);
            $msg = [
                'success' => 'Branch Updated Successfully',
                'reload' => true
            ];
        } else {
            $branch = new Branch();
            $msg = [
                'success' => 'Branch Created Successfully',
                'redirect' => route('branch.index')
            ];
        }

        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->number = $request->number;
        $branch->user_id = $request->filled('user_id') ? $request->user_id : null;
        $branch->save();

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
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()->route('branch.index')->with('success', 'Branch deleted successfully.');

    }

    /**
     * Switch active branch context in session.
     */
    public function switchBranch(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user && ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin'));

        if (!$isSuperAdmin && $user && !empty($user->branch_id)) {
            session(['active_branch_id' => $user->branch_id]);
            return redirect()->back()->with('error', 'You are restricted to your assigned branch.');
        }

        $branchId = $request->input('branch_id');
        if ($branchId === 'all') {
            if ($isSuperAdmin) {
                session(['active_branch_id' => 'all']);
            }
        } else {
            $branch = Branch::find($branchId);
            if ($branch) {
                session(['active_branch_id' => $branch->id]);
            }
        }

        return redirect()->back()->with('success', 'Branch context updated successfully.');
    }
}
