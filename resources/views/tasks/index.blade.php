@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Daftar Tugas</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ Tambah Tugas</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($tasks->isEmpty())
        <div class="alert alert-info text-center">
            <strong>Tidak ada tugas yang ditemukan.</strong>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Proyek</th>
                                <th>Deskripsi</th>
                                <th>Dibuat Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge {{ $task->status === 'completed' ? 'bg-success' : ($task->status === 'in progress' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $task->project->name }}</td>
                                    <td>{!!$task->description->text!!}</td>
                                    <td>{{ $task->createdBy->name }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><br>
    @endif
    <a href="/dashboard" class="btn btn-secondary">Back</a>
</div>
@endsection
