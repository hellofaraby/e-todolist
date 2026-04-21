<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'todo';

    protected $fillable = [
        'task',
        'is_done',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'due_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_todo');
    }

    public static function priorityOrderSql(): string
    {
        return "CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END";
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'danger',
            'medium' => 'warning text-dark',
            default => 'success',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_done ? 'success' : 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_done ? 'Selesai' : 'Pending';
    }

    public function getDeadlineStateAttribute(): ?string
    {
        if (! $this->due_date) {
            return null;
        }

        if ($this->is_done) {
            return 'done';
        }

        $now = now();

        if ($this->due_date->isPast()) {
            return 'overdue';
        }

        if ($this->due_date->lessThanOrEqualTo($now->copy()->addDay())) {
            return 'soon';
        }

        return 'upcoming';
    }

    public function getDeadlineBadgeClassAttribute(): ?string
    {
        return match ($this->deadline_state) {
            'overdue' => 'danger',
            'soon' => 'warning text-dark',
            'done' => 'success',
            'upcoming' => 'info text-dark',
            default => null,
        };
    }

    public function getDeadlineLabelAttribute(): ?string
    {
        if (! $this->due_date) {
            return null;
        }

        return match ($this->deadline_state) {
            'overdue' => 'Overdue',
            'soon' => 'Mendekati deadline',
            'done' => 'Deadline aman',
            default => 'Terjadwal',
        };
    }

    public function getFormattedDueDateAttribute(): ?string
    {
        if (! $this->due_date) {
            return null;
        }

        return Carbon::parse($this->due_date)->translatedFormat('d M Y, H:i');
    }
}
