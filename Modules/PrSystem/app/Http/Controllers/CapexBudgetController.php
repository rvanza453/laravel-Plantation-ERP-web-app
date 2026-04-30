<?php

namespace Modules\PrSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PrSystem\Models\CapexAsset;
use Modules\PrSystem\Models\CapexBudget;
use Modules\PrSystem\Models\Department;

class CapexBudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = CapexBudget::with(['department', 'capexAsset'])->latest()->get();
        $departments = Department::orderBy('name')->get();
        $assets = CapexAsset::active()->orderBy('name')->get();
        
        return view('prsystem::admin.capex.budgets.index', compact('budgets', 'departments', 'assets'));
    }

    public function edit(CapexBudget $budget)
    {
        $budget->load(['department', 'capexAsset'])->loadCount('capexRequests');
        $departments = Department::orderBy('name')->get();
        $assets = CapexAsset::active()->orderBy('name')->get();

        $usedAmount = max(0, (float) $budget->amount + (float) ($budget->pta_amount ?? 0) - (float) $budget->remaining_amount);
        $remainingQuantity = (int) ($budget->remaining_quantity ?? $budget->original_quantity);
        $usedQuantity = max(0, (int) $budget->original_quantity - $remainingQuantity);

        return view('prsystem::admin.capex.budgets.edit', compact(
            'budget',
            'departments',
            'assets',
            'usedAmount',
            'usedQuantity'
        ));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'capex_asset_id' => 'required|exists:capex_assets,id',
            // 'budget_code' => 'auto-generated',
            'amount' => 'required|numeric|min:0',
            'original_quantity' => 'required|integer|min:1',
            'is_budgeted' => 'boolean',
            'fiscal_year' => 'required|integer|min:2020|max:2099',
        ]);

        $validated['remaining_amount'] = $validated['amount'];
        $validated['remaining_quantity'] = $validated['original_quantity'];
        $validated['is_budgeted'] = $request->has('is_budgeted');
        
        \Modules\PrSystem\Models\CapexBudget::create($validated);

        return redirect()->route('admin.capex.budgets.index')->with('success', 'Capex Budget Created Successfully');
    }

    public function update(\Illuminate\Http\Request $request, CapexBudget $budget)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'capex_asset_id' => 'required|exists:capex_assets,id',
            'amount' => 'required|numeric|min:0',
            'original_quantity' => 'required|integer|min:1',
            'fiscal_year' => 'required|integer|min:2020|max:2099',
            'is_budgeted' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $hasRequests = $budget->capexRequests()->exists();

        if ($hasRequests && (
            (int) $validated['department_id'] !== (int) $budget->department_id ||
            (int) $validated['capex_asset_id'] !== (int) $budget->capex_asset_id
        )) {
            return back()
                ->withInput()
                ->with('error', 'Budget yang sudah dipakai tidak dapat dipindah department atau asset-nya.');
        }

        $usedAmount = max(0, (float) $budget->amount + (float) ($budget->pta_amount ?? 0) - (float) $budget->remaining_amount);
        $usedQuantity = max(0, (int) $budget->original_quantity - (int) ($budget->remaining_quantity ?? $budget->original_quantity));
        $newTotalAmount = (float) $validated['amount'] + (float) ($budget->pta_amount ?? 0);

        if ($newTotalAmount < $usedAmount) {
            return back()
                ->withInput()
                ->with('error', 'Total budget baru tidak boleh lebih kecil dari nilai yang sudah terpakai sebesar Rp ' . number_format($usedAmount, 0, ',', '.') . '.');
        }

        if ((int) $validated['original_quantity'] < $usedQuantity) {
            return back()
                ->withInput()
                ->with('error', 'Total quantity baru tidak boleh lebih kecil dari quantity yang sudah terpakai sebanyak ' . $usedQuantity . ' unit.');
        }

        DB::transaction(function () use ($budget, $validated, $usedAmount, $usedQuantity, $newTotalAmount, $request) {
            $budget->department_id = $validated['department_id'];
            $budget->capex_asset_id = $validated['capex_asset_id'];
            $budget->amount = $validated['amount'];
            $budget->remaining_amount = max(0, $newTotalAmount - $usedAmount);
            $budget->original_quantity = $validated['original_quantity'];
            $budget->remaining_quantity = max(0, $validated['original_quantity'] - $usedQuantity);
            $budget->is_budgeted = $request->boolean('is_budgeted');
            $budget->is_active = $request->boolean('is_active');
            $budget->fiscal_year = $validated['fiscal_year'];
            $budget->save();
        });

        return redirect()->route('admin.capex.budgets.index')->with('success', 'Budget CAPEX berhasil diperbarui.');
    }

    public function destroy(CapexBudget $budget)
    {
        $budget->delete();
        return redirect()->route('admin.capex.budgets.index')->with('success', 'Budget CAPEX berhasil dihapus.');
    }

    public function addPta(Request $request, CapexBudget $budget)
    {
        $validated = $request->validate([
            'pta_amount' => 'required|numeric|min:1',
        ]);

        $budget->pta_amount += $validated['pta_amount'];
        $budget->remaining_amount += $validated['pta_amount'];
        $budget->save();

        return redirect()->route('admin.capex.budgets.index')->with('success', 'PTA (Tambahan Anggaran) berhasil ditambahkan.');
    }
}
