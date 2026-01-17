<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md bg-white p-8 shadow-md rounded-lg">
    <h2 class="text-2xl font-bold text-center mb-6 text-indigo-600">Masuk Akun</h2>

    @if (session('success'))
        <div class="text-green-600 text-center mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="flex flex-col gap-4">
        @csrf

        <input 
            type="text" 
            name="username" 
            placeholder="Username" 
            class="p-3 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600" 
            required
        >
        
        <input 
            type="password" 
            name="password" 
            placeholder="Kata Sandi" 
            class="p-3 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600" 
            required
        >
        
        @if ($errors->any())
            <div class="text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <button 
            type="submit" 
            class="btn-primary mt-2"
        >
            Masuk
        </button>

        <a 
            href="{{ url('/register') }}" 
            class="btn-secondary mt-2 text-center w-full md:w-auto"
        >
            Belum punya akun? Daftar di sini
        </a>
    </form>
</div>
@endsection
