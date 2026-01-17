@extends('clients.dashboard_clients')


@section('clients-content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Bantuan & Kontak</h2>

<div class="bg-white p-6 rounded-xl shadow">
    <p class="mb-4">Jika Anda mengalami kendala, silakan hubungi tim kami melalui formulir berikut:</p>

    <form class="space-y-4">
        <input type="text" placeholder="Nama Anda" class="form-input w-full">
        <input type="email" placeholder="Email Anda" class="form-input w-full">
        <textarea placeholder="Pesan Anda" rows="4" class="form-input w-full"></textarea>

        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold">
            Kirim Pesan
        </button>
    </form>
</div>
@endsection
