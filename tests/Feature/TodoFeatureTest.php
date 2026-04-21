<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Laravel 11 Advanced Todo');
    }

    public function test_store_parses_tags_priority_and_due_date_from_input(): void
    {
        $response = $this->post('/', [
            'task' => 'Bayar listrik besok jam 9 #rumah !high',
            'priority' => 'medium',
            'due_date' => null,
        ]);

        $response->assertRedirect(route('todo'));

        $todo = Todo::with('tags')->firstOrFail();

        $this->assertSame('Bayar listrik', $todo->task);
        $this->assertSame('high', $todo->priority);
        $this->assertNotNull($todo->due_date);
        $this->assertCount(1, $todo->tags);
        $this->assertSame('rumah', $todo->tags->first()->name);
    }

    public function test_advanced_search_supports_tag_status_and_priority(): void
    {
        $tagKerja = Tag::create(['name' => 'kerja', 'color' => '#0d6efd']);
        $tagRumah = Tag::create(['name' => 'rumah', 'color' => '#198754']);

        $todoA = Todo::create([
            'task' => 'Meeting client',
            'priority' => 'high',
            'is_done' => false,
        ]);
        $todoA->tags()->sync([$tagKerja->id]);

        $todoB = Todo::create([
            'task' => 'Rapikan dokumen',
            'priority' => 'medium',
            'is_done' => false,
        ]);
        $todoB->tags()->sync([$tagRumah->id]);

        $todoC = Todo::create([
            'task' => 'Kirim invoice',
            'priority' => 'high',
            'is_done' => true,
        ]);
        $todoC->tags()->sync([$tagKerja->id]);

        $response = $this->get('/?search=%23kerja+is%3Apending+priority%3Ahigh');

        $response->assertOk();
        $response->assertSee('Meeting client');
        $response->assertDontSee('Rapikan dokumen');
        $response->assertDontSee('Kirim invoice');
    }

    public function test_soft_deleted_task_can_be_restored(): void
    {
        $todo = Todo::create([
            'task' => 'Task sementara',
            'priority' => 'medium',
            'is_done' => false,
        ]);

        $this->delete(route('todo.delete', ['id' => $todo->id]))
            ->assertRedirect();

        $this->assertSoftDeleted('todo', ['id' => $todo->id]);

        $this->patch(route('todo.restore', ['id' => $todo->id]))
            ->assertRedirect(route('todo', ['view' => 'trash']));

        $this->assertDatabaseHas('todo', [
            'id' => $todo->id,
            'deleted_at' => null,
        ]);
    }
}
