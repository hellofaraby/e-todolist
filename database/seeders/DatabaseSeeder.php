<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Todo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Todo::query()->delete();
        Tag::query()->delete();

        $seedTodos = [
            [
                'task' => 'Meeting client',
                'priority' => 'high',
                'due_date' => now()->addHours(6),
                'is_done' => false,
                'tags' => [
                    ['name' => 'kerja', 'color' => '#0d6efd'],
                    ['name' => 'urgent', 'color' => '#dc3545'],
                ],
            ],
            [
                'task' => 'Bayar listrik',
                'priority' => 'medium',
                'due_date' => now()->addDay()->setTime(9, 0),
                'is_done' => false,
                'tags' => [
                    ['name' => 'rumah', 'color' => '#198754'],
                ],
            ],
            [
                'task' => 'Review proposal proyek',
                'priority' => 'high',
                'due_date' => now()->subHours(3),
                'is_done' => false,
                'tags' => [
                    ['name' => 'kerja', 'color' => '#0d6efd'],
                ],
            ],
            [
                'task' => 'Belanja mingguan',
                'priority' => 'low',
                'due_date' => null,
                'is_done' => true,
                'tags' => [
                    ['name' => 'pribadi', 'color' => '#fd7e14'],
                ],
            ],
        ];

        foreach ($seedTodos as $item) {
            $todo = Todo::create([
                'task' => $item['task'],
                'priority' => $item['priority'],
                'due_date' => $item['due_date'],
                'is_done' => $item['is_done'],
            ]);

            $tagIds = collect($item['tags'])
                ->map(fn (array $tag) => Tag::firstOrCreate(
                    ['name' => $tag['name']],
                    ['color' => $tag['color']]
                )->id)
                ->all();

            $todo->tags()->sync($tagIds);
        }

        Todo::create([
            'task' => 'Task yang sudah dibuang',
            'priority' => 'low',
            'due_date' => now()->addDays(3),
            'is_done' => false,
        ])->delete();
    }
}
