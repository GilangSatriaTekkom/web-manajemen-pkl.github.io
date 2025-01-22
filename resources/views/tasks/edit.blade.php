@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Task</h1>
    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" id="title" value="{{ $task->title }}" required>
        </div>
        
        <div class="form-group">
            <label for="board_id">Board</label>
            <select class="form-control" name="board_id" id="board_id" required>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" {{ $task->board_id == $board->id ? 'selected' : '' }}>
                        {{ $board->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status" id="status" required>
                <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                <option value="paused" {{ $task->status == 'paused' ? 'selected' : '' }}>Paused</option>
            </select>
        </div>

        <div class="form-group">
            <label for="project_id">Project</label>
            <select class="form-control" name="project_id" id="project_id" required>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $task->project_id == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description_id">Description</label>
            <select class="form-control" name="description_id" id="description_id" required>
                @foreach($descriptions as $description)
                    <option value="{{ $description->id }}" {{ $task->description_id == $description->id ? 'selected' : '' }}>
                        {{ $description->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
</div>
@endsection
