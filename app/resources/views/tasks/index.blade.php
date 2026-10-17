<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Simple Task App</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-100 min-h-screen text-slate-800">
        <div class="max-w-3xl mx-auto py-12 px-4">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.2em] text-indigo-600 font-semibold">Laravel</p>
                        <h1 class="text-3xl font-bold">Simple Task List</h1>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->count() }} tasks
                    </span>
                </div>

                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('tasks.store') }}" method="POST" class="mb-8 grid gap-4 md:grid-cols-[1fr_1.2fr_auto]">
                    @csrf
                    <input
                        type="text"
                        name="title"
                        placeholder="Task title"
                        class="border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required
                    >
                    <input
                        type="text"
                        name="description"
                        placeholder="Optional description"
                        class="border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                    <button type="submit" class="bg-indigo-600 text-white rounded-lg px-5 py-3 font-medium hover:bg-indigo-700 transition">
                        Add Task
                    </button>
                </form>

                <div class="space-y-4">
                    @forelse ($tasks as $task)
                        <div class="rounded-xl border border-slate-200 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline-flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="completed" value="0">
                                            <input
                                                type="checkbox"
                                                name="completed"
                                                value="1"
                                                onchange="this.form.submit()"
                                                {{ $task->completed ? 'checked' : '' }}
                                                class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500"
                                            >
                                        </form>
                                        <h2 class="text-lg font-semibold {{ $task->completed ? 'line-through text-slate-400' : '' }}">
                                            {{ $task->title }}
                                        </h2>
                                    </div>

                                    @if ($task->description)
                                        <p class="mt-2 text-sm text-slate-600">{{ $task->description }}</p>
                                    @endif
                                </div>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                            No tasks yet. Add your first one above.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </body>
</html>
