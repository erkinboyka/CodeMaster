@extends('layouts.admin')

@section('title', __('Notifications') . ' - CodeMaster')
@section('header-title', __('Notifications'))
@section('header-subtitle', __('Manage platform notifications'))

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Message') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($notifications as $notif)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-800">{{ $notif->user->name ?? __('Unknown') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600 max-w-md truncate">{{ $notif->message }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500">{{ $notif->notification_time?->format('M d, Y H:i') ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $notif->is_read ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">{{ $notif->is_read ? __('Read') : __('Unread') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.notifications.delete', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this notification?') }}')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete') }}">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">{{ __('No notifications found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
