<x-admin-layout>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Log Aktivitas</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sistem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan Aktivitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $activity->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ ($activity->system ?? 'OTHER') === 'SAS' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $activity->system ?? 'OTHER' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $activity->action }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $activity->description }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div>{{ $activity->route_name ?? '-' }}</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 bg-gray-100 text-gray-700 font-semibold">{{ strtoupper($activity->http_method ?? '-') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $activity->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $activities->links() }}</div>
    </div>
</x-admin-layout>
