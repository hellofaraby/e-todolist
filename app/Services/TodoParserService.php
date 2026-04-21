<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class TodoParserService
{
    public function parseTodoInput(string $input): array
    {
        $workingText = trim($input);
        $tags = $this->extractTags($workingText);
        $priority = $this->extractPriority($workingText);
        $dueDate = $this->extractDueDate($workingText);

        $task = $this->cleanTask($workingText);

        return [
            'original' => $input,
            'task' => $task !== '' ? $task : trim($input),
            'tags' => $tags,
            'priority' => $priority,
            'due_date' => $dueDate?->format('Y-m-d H:i:s'),
        ];
    }

    public function parseSearchQuery(string $query): array
    {
        $filters = [
            'raw' => trim($query),
            'keyword' => '',
            'tags' => [],
            'status' => null,
            'priority' => null,
        ];

        if ($filters['raw'] === '') {
            return $filters;
        }

        preg_match_all('/#([\pL\pN_-]+)/u', $query, $tagMatches);
        $filters['tags'] = collect($tagMatches[1] ?? [])
            ->map(fn (string $tag) => Str::lower($tag))
            ->unique()
            ->values()
            ->all();

        if (preg_match('/\bis:(done|pending)\b/i', $query, $statusMatch)) {
            $filters['status'] = Str::lower($statusMatch[1]);
        }

        if (preg_match('/\bpriority:(low|medium|high)\b/i', $query, $priorityMatch)) {
            $filters['priority'] = Str::lower($priorityMatch[1]);
        }

        $keyword = preg_replace('/#([\pL\pN_-]+)/u', ' ', $query);
        $keyword = preg_replace('/\bis:(done|pending)\b/i', ' ', (string) $keyword);
        $keyword = preg_replace('/\bpriority:(low|medium|high)\b/i', ' ', (string) $keyword);
        $filters['keyword'] = trim(preg_replace('/\s+/', ' ', (string) $keyword) ?? '');

        return $filters;
    }

    public function generateTagColor(string $tagName): string
    {
        $palette = [
            '#0d6efd',
            '#198754',
            '#dc3545',
            '#6f42c1',
            '#fd7e14',
            '#20c997',
            '#0dcaf0',
            '#6610f2',
        ];

        return $palette[abs(crc32(Str::lower($tagName))) % count($palette)];
    }

    protected function extractTags(string &$text): array
    {
        preg_match_all('/#([\pL\pN_-]+)/u', $text, $matches);

        $text = preg_replace('/#([\pL\pN_-]+)/u', ' ', $text) ?? $text;

        return collect($matches[1] ?? [])
            ->map(fn (string $tag) => Str::lower($tag))
            ->unique()
            ->values()
            ->all();
    }

    protected function extractPriority(string &$text): ?string
    {
        if (! preg_match('/!(low|medium|high)\b/i', $text, $match)) {
            return null;
        }

        $text = preg_replace('/!(low|medium|high)\b/i', ' ', $text) ?? $text;

        return Str::lower($match[1]);
    }

    protected function extractDueDate(string &$text): ?Carbon
    {
        $timezone = config('app.timezone');
        $now = now($timezone);

        $patterns = [
            '/\bbesok(?:\s+jam\s+|\s+)(\d{1,2})(?::(\d{2}))?\b/i' => fn (array $match) => $now->copy()->addDay()->setTime((int) $match[1], (int) ($match[2] ?? 0)),
            '/\bhari\s+ini(?:\s+jam\s+|\s+)(\d{1,2})(?::(\d{2}))?\b/i' => fn (array $match) => $now->copy()->setTime((int) $match[1], (int) ($match[2] ?? 0)),
            '/\blusa(?:\s+jam\s+|\s+)(\d{1,2})(?::(\d{2}))?\b/i' => fn (array $match) => $now->copy()->addDays(2)->setTime((int) $match[1], (int) ($match[2] ?? 0)),
            '/\b(\d{4}-\d{2}-\d{2})\s+(\d{1,2}:\d{2})\b/' => fn (array $match) => Carbon::createFromFormat('Y-m-d H:i', $match[1] . ' ' . $match[2], $timezone),
            '/\b(\d{4}-\d{2}-\d{2})\b/' => fn (array $match) => Carbon::createFromFormat('Y-m-d H:i', $match[1] . ' 09:00', $timezone),
        ];

        foreach ($patterns as $pattern => $resolver) {
            if (! preg_match($pattern, $text, $match)) {
                continue;
            }

            $text = preg_replace($pattern, ' ', $text, 1) ?? $text;

            return $resolver($match);
        }

        return null;
    }

    protected function cleanTask(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $text));
    }
}
