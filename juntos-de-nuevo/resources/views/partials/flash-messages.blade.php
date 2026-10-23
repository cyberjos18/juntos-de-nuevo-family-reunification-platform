@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <x-alert type="success" :message="session('success')" />
    </div>
@endif

@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <x-alert type="error" :message="session('error')" />
    </div>
@endif

@if(session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <x-alert type="warning" :message="session('warning')" />
    </div>
@endif
