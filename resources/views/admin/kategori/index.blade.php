<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kategori') }}
        </h2>
    </x-slot>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}  
        </div>
    @endif

</x-app-layout>