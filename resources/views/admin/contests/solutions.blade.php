@extends('layouts.admin')

@section('title', __('Contest Solutions') . ' - CodeMaster')
@section('header-title', __('Contest Solutions'))
@section('header-subtitle', __('View all contest submissions'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <form action="{{ route('admin.contests.solutions') }}" method="GET" class="flex flex-wrap gap-3">
            <select name="contest_id" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">{{ __('All Contests') }}</option>
                @foreach($contests as $c)
                    <option value="{{ $c->id }}" {{ request('contest_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition"><i class="fas fa-filter mr-1"></i>{{ __('Filter') }}</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('User') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Contest') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Task') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($submissions as $sub)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $sub->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->contest->title ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->problem->title ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php $sc = match($sub->status) { 'accepted','done' => 'green', 'pending','in_progress' => 'yellow', default => 'red' }; @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ $sub->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->created_at?->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button onclick="showDetail('contest',{{ $sub->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-eye text-sm"></i></button>
                            <form action="{{ route('admin.contests.submissions.reset', $sub->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this submission?') }}')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">{{ __('No submissions found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $submissions->withQueryString()->links() }}</div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">{{ __('Submission Detail') }}</h3>
            <button onclick="document.getElementById('detailModal').classList.add('hidden');document.getElementById('detailModal').classList.remove('flex')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div id="detailContent" class="text-sm text-gray-600 space-y-2">
            <p>{{ __('Loading...') }}</p>
        </div>
    </div>
</div>

<script>
function showDetail(kind, id) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<p>Loading...</p>';

    fetch(`/admin/submission-detail?kind=${kind}&id=${id}`)
        .then(r => r.json())
        .then(d => {
            content.innerHTML = `
                <p><strong>User:</strong> ${d.user}</p>
                <p><strong>Status:</strong> ${d.status}</p>
                <p><strong>Contest:</strong> ${d.contest || '-'}</p>
                <p><strong>Problem:</strong> ${d.problem || '-'}</p>
                <p><strong>Date:</strong> ${d.created_at}</p>
                <pre class="bg-gray-50 p-4 rounded-xl overflow-x-auto text-xs mt-2 max-h-96 overflow-y-auto">${d.code}</pre>
            `;
        })
        .catch(() => content.innerHTML = '<p class="text-red-500">Error loading detail.</p>');
}
</script>
@endsection
