<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->orderBy('name')->get();
        return view('admin_panel.assets.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:100',
            'code'              => 'nullable|string|max:10',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'description'       => 'nullable|string',
        ]);

        if ($request->filled('edit_id')) {
            $cat = AssetCategory::findOrFail($request->edit_id);
            $cat->update([
                'name'              => $request->name,
                'code'              => strtoupper(trim($request->code)),
                'depreciation_rate' => $request->depreciation_rate ?? 10.0,
                'description'       => $request->description,
                'status'            => $request->has('status') ? 1 : 0,
            ]);
            return redirect()->back()->with('success', 'Category updated successfully.');
        }

        AssetCategory::create([
            'name'              => $request->name,
            'code'              => strtoupper(trim($request->code)),
            'depreciation_rate' => $request->depreciation_rate ?? 10.0,
            'description'       => $request->description,
            'status'            => 1,
        ]);

        return redirect()->back()->with('success', 'Asset category created successfully.');
    }

    public function destroy($id)
    {
        $cat = AssetCategory::withCount('assets')->findOrFail($id);
        if ($cat->assets_count > 0) {
            return redirect()->back()->with('error', "Cannot delete category '{$cat->name}' because it contains {$cat->assets_count} assets.");
        }
        $cat->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
