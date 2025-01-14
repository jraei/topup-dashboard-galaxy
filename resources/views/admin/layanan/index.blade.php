<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Layanan') }}
        </h2>
    </x-slot>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}  
        </div>
    @endif

    <div class="max-w-2xl bg-slate-500">
        <a class="bg-blue-400 py-2 px-3 rounded-lg text-white" href="{{ route('layanan.getService') }}">Get Layanan</a>
    </div>

</x-app-layout>