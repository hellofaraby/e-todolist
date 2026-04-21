<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Tag;
use App\Models\Todo;
use App\Services\TodoParserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function __construct(
        protected TodoParserService $parser
    ) {
    }

    public function index(Request $request): View
    {
        $searchFilters = $this->parser->parseSearchQuery((string) $request->string('search'));
        $isTrashView = $request->string('view')->toString() === 'trash';

        $todosQuery = Todo::query()
            ->with('tags')
            ->when($isTrashView, fn (Builder $query) => $query->onlyTrashed())
            ->when(! $isTrashView, fn (Builder $query) => $query->orderByRaw(Todo::priorityOrderSql()))
            ->when(! $isTrashView, fn (Builder $query) => $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC'))
            ->when(! $isTrashView, fn (Builder $query) => $query->orderBy('created_at', 'desc'))
            ->when($isTrashView, fn (Builder $query) => $query->latest('deleted_at'));

        $this->applySearchFilters($todosQuery, $searchFilters);

        $data = $todosQuery->paginate(10)->withQueryString();

        $stats = $this->buildStats();
        $tags = Tag::query()
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return view('todo.app', [
            'data' => $data,
            'stats' => $stats,
            'tags' => $tags,
            'searchFilters' => $searchFilters,
            'trashCount' => Todo::onlyTrashed()->count(),
            'isTrashView' => $isTrashView,
            'parserExamples' => [
                'Meeting client #kerja #urgent !high',
                'Bayar listrik besok jam 9 #rumah !high',
                '#kerja is:pending priority:high',
            ],
        ]);
    }

    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $parsed = $this->parser->parseTodoInput($request->validated('task'));

        DB::transaction(function () use ($request, $parsed): void {
            $todo = Todo::create([
                'task' => $parsed['task'],
                'priority' => $parsed['priority'] ?? $request->validated('priority', 'medium'),
                'due_date' => $parsed['due_date'] ?? $request->validated('due_date'),
                'is_done' => false,
            ]);

            $this->syncTags($todo, $parsed['tags']);
        });

        return redirect()
            ->route('todo')
            ->with('success', 'Tugas berhasil ditambahkan dan diproses secara otomatis.');
    }

    public function update(UpdateTodoRequest $request, string $id): RedirectResponse
    {
        $todo = Todo::with('tags')->findOrFail($id);
        $parsed = $this->parser->parseTodoInput($request->validated('task'));

        DB::transaction(function () use ($request, $todo, $parsed): void {
            $todo->update([
                'task' => $parsed['task'],
                'priority' => $parsed['priority'] ?? $request->validated('priority', 'medium'),
                'due_date' => $parsed['due_date'] ?? $request->validated('due_date'),
                'is_done' => (bool) $request->boolean('is_done'),
            ]);

            $this->syncTags($todo, $parsed['tags']);
        });

        return redirect()
            ->back()
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return redirect()
            ->back()
            ->with('success', 'Tugas berhasil dipindahkan ke arsip.');
    }

    public function restore(string $id): RedirectResponse
    {
        $todo = Todo::onlyTrashed()->findOrFail($id);
        $todo->restore();

        return redirect()
            ->route('todo', ['view' => 'trash'])
            ->with('success', 'Tugas berhasil dipulihkan.');
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $todo = Todo::onlyTrashed()->findOrFail($id);
        $todo->forceDelete();

        return redirect()
            ->route('todo', ['view' => 'trash'])
            ->with('success', 'Tugas berhasil dihapus secara permanen.');
    }

    protected function syncTags(Todo $todo, array $tagNames): void
    {
        $tagIds = collect($tagNames)
            ->map(fn (string $tagName) => Tag::firstOrCreate(
                ['name' => $tagName],
                ['color' => $this->parser->generateTagColor($tagName)]
            )->id)
            ->all();

        $todo->tags()->sync($tagIds);
    }

    protected function applySearchFilters(Builder $query, array $filters): void
    {
        if ($filters['keyword'] !== '') {
            $query->where('task', 'like', '%' . $filters['keyword'] . '%');
        }

        if ($filters['status'] === 'done') {
            $query->where('is_done', true);
        }

        if ($filters['status'] === 'pending') {
            $query->where('is_done', false);
        }

        if ($filters['priority'] !== null) {
            $query->where('priority', $filters['priority']);
        }

        foreach ($filters['tags'] as $tagName) {
            $query->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->where('name', $tagName));
        }
    }

    protected function buildStats(): array
    {
        $baseQuery = Todo::query();
        $total = (clone $baseQuery)->count();
        $done = (clone $baseQuery)->where('is_done', true)->count();
        $pending = max($total - $done, 0);
        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        return [
            'total' => $total,
            'done' => $done,
            'pending' => $pending,
            'progress' => $progress,
        ];
    }
}
