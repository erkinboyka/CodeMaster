@if(session('success') || session('error') || session('warning') || session('info'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="fixed top-24 right-4 z-50 max-w-sm w-full">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-lg mb-2">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-lg"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-lg mb-2">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    @if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-lg mb-2">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-yellow-700 font-medium">{{ session('warning') }}</p>
            </div>
            <button @click="show = false" class="text-yellow-400 hover:text-yellow-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    @if(session('info'))
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg shadow-lg mb-2">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-lg"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-blue-700 font-medium">{{ session('info') }}</p>
            </div>
            <button @click="show = false" class="text-blue-400 hover:text-blue-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
</div>
@endif
