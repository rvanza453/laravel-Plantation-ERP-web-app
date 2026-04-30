<?php

namespace Modules\PrSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\PrSystem\Models\Product;
use Modules\PrSystem\Models\ProductRequest;
use Modules\PrSystem\Models\Site;

class ProductRequestController extends Controller
{
    private function currentRole(): ?string
    {
        return auth()->user()?->moduleRole('pr');
    }

    private function ensureRequesterAccess(): void
    {
        if (in_array($this->currentRole(), ['Admin', 'Approver'], true)) {
            abort(403, 'Fitur usulan product hanya tersedia untuk user non-admin dan non-approver.');
        }
    }

    private function ensureAdminAccess(): void
    {
        if ($this->currentRole() !== 'Admin') {
            abort(403, 'Fitur ini khusus untuk Admin.');
        }
    }

    public function index()
    {
        $this->ensureRequesterAccess();

        $requests = ProductRequest::with(['requester', 'decisionBy'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->get();

        $categories = config('prsystem.options.product_categories', ['Sparepart', 'Consumable']);

        return view('prsystem::product_requests.index', compact('requests', 'categories'));
    }

    public function store(Request $request)
    {
        $this->ensureRequesterAccess();

        $categories = config('prsystem.options.product_categories', ['Sparepart', 'Consumable']);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'code'),
                Rule::unique('product_requests', 'code')->where(function ($query) {
                    $query->where('status', 'Pending');
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'category' => ['required', Rule::in($categories)],
            'price_estimation' => ['required', 'numeric', 'min:0'],
            'reference_link' => ['required', 'url', 'max:2048'],
        ]);

        ProductRequest::create([
            'requester_id' => auth()->id(),
            'status' => 'Pending',
            'decision_by' => null,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'category' => $validated['category'],
            'price_estimation' => $validated['price_estimation'],
            'min_stock' => 0,
            'reference_link' => $validated['reference_link'],
            'site_id' => auth()->user()->site_id,
        ]);

        return back()->with('success', 'Usulan produk berhasil dikirim dan menunggu pengecekan admin.');
    }

    public function adminIndex()
    {
        $this->ensureAdminAccess();

        $requests = ProductRequest::with(['requester', 'decisionBy'])
            ->latest()
            ->paginate(10);

        return view('prsystem::admin.product_requests.index', compact('requests'));
    }

    public function approve(ProductRequest $productRequest)
    {
        $this->ensureAdminAccess();

        if ($productRequest->status !== 'Pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        if (Product::where('code', $productRequest->code)->exists()) {
            return back()->with('error', 'Kode produk sudah ada di master product. Silakan reject request ini atau minta revisi kode.');
        }

        DB::transaction(function () use ($productRequest) {
            $product = Product::create([
                'code' => $productRequest->code,
                'name' => $productRequest->name,
                'unit' => $productRequest->unit,
                'category' => $productRequest->category,
                'price_estimation' => $productRequest->price_estimation,
                'min_stock' => $productRequest->min_stock ?? 0,
            ]);

            // Attach the site from the product request to the product
            if ($productRequest->site_id) {
                $product->sites()->attach($productRequest->site_id);
            }

            $productRequest->update([
                'status' => 'Approved',
                'decision_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Request produk berhasil disetujui dan sudah masuk ke master product.');
    }

    public function reject(ProductRequest $productRequest)
    {
        $this->ensureAdminAccess();

        if ($productRequest->status !== 'Pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $productRequest->update([
            'status' => 'Rejected',
            'decision_by' => auth()->id(),
        ]);

        return back()->with('success', 'Request produk ditolak.');
    }
}