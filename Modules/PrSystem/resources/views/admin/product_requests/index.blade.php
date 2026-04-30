<x-prsystem::app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Product Requests') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Admin dapat approve atau reject request produk dari user.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Requester</th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Detail</th>
                                <th class="px-4 py-3">Reference</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($requests as $request)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $request->requester?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->requester?->position ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $request->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $request->code }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <div>{{ $request->category }}</div>
                                        <div>{{ $request->unit }}</div>
                                        <div>Rp {{ number_format($request->price_estimation, 0, ',', '.') }}</div>
                                        <div>Min Stock: {{ $request->min_stock }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ $request->reference_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 break-all">{{ $request->reference_link }}</a>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status === 'Approved' ? 'bg-green-100 text-green-800' : ($request->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $request->status }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">Decision: {{ $request->decisionBy?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if($request->status === 'Pending')
                                            <div class="flex justify-center gap-2">
                                                <form method="POST" action="{{ route('admin.product-requests.approve', $request) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-md text-xs font-semibold border border-green-200">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.product-requests.reject', $request) }}" onsubmit="return confirm('Tolak request produk ini?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-md text-xs font-semibold border border-red-200">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada request produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</x-prsystem::app-layout>