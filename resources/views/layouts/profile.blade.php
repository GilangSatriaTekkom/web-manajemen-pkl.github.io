@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil Pengguna</h1>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $user->id }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Email Verified At</th>
            <td>{{ $user->email_verified_at }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ $user->roles }}</td>
        </tr>
        <tr>
            <th>Asal Sekolah</th>
            <td>{{ $user->asal_sekolah }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $user->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $user->updated_at }}</td>
        </tr>
    </table>
</div>
@endsection
