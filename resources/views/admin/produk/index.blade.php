<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Produk') }}
        </h2>
    </x-slot>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}  
        </div>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <h2 class="font-semibold text-slate-500">{{$error}}</h2>
        @endforeach
    @endif  

    <div class="max-w-2xl bg-slate-500">
        <a class="bg-blue-400 py-2 px-3 rounded-lg text-white" href="/produk/getProduk">Get Produk</a>
    </div>
</x-app-layout>