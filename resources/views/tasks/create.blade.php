@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Task</h1>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" id="title" required>
        </div>
        
        <div class="form-group">
            <label for="board_id">Board</label>
            <select class="form-control" name="board_id" id="board_id" required>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}">{{ $board->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status" id="status" required>
                <option value="to_do">To Do</option>
                <option value="in_progress">In Progress</option>
                <option value="done">Done</option>
                <option value="paused">Paused</option>
            </select>
        </div>

        <div class="form-group">
            <label for="project_id">Project</label>
            <select class="form-control" name="project_id" id="project_id" required>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description_id">Description Berdasarkan ID</label>
            <select class="form-control" name="description_id" id="description_id" required>
                @foreach($descriptions as $description)
                    <option value="{{ $description->id }}">{!!$description->id!!}</option>
                @endforeach
            </select>
        </div> <br>

        <button type="submit" class="btn btn-primary">Create Task</button>
    </form><br>

    <a href="/dashboard" class="btn btn-secondary">Back</a>
</div>
@endsection
