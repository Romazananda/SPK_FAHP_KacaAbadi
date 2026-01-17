<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 shadow-md rounded-lg">
        <h2 class="text-2xl font-bold text-center mb-6 text-indigo-600">Daftar Akun</h2>

        <form action="{{ url('/register') }}" method="POST" class="flex flex-col gap-4">
            @csrf

            <input 
                type="text" 
                name="name" 
                placeholder="Nama Lengkap" 
                class="p-3 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600" 
                required
            >
            
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
            
            <input 
                type="password" 
                name="password_confirmation" 
                placeholder="Konfirmasi Kata Sandi" 
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
                class="btn-primary mt-2 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
            >
                Daftar
            </button>

            <a 
                href="{{ url('/login') }}" 
                class="btn-secondary mt-2 text-center text-indigo-600 hover:text-indigo-700"
            >
                Sudah punya akun? Masuk di sini
            </a>
        </form>
    </div>
</div>
@endsection
