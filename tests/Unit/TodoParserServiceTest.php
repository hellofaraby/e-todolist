<?php

namespace Tests\Unit;

use App\Services\TodoParserService;
use PHPUnit\Framework\TestCase;

class TodoParserServiceTest extends TestCase
{
    public function test_it_parses_indonesian_smart_input(): void
    {
        $service = new TodoParserService();

        $result = $service->parseTodoInput('Bayar listrik besok jam 9 #rumah !high');

        $this->assertSame('Bayar listrik', $result['task']);
        $this->assertSame(['rumah'], $result['tags']);
        $this->assertSame('high', $result['priority']);
        $this->assertNotNull($result['due_date']);
    }

    public function test_it_parses_advanced_search_query(): void
    {
        $service = new TodoParserService();

        $result = $service->parseSearchQuery('#kerja is:pending priority:high revisi');

        $this->assertSame('revisi', $result['keyword']);
        $this->assertSame(['kerja'], $result['tags']);
        $this->assertSame('pending', $result['status']);
        $this->assertSame('high', $result['priority']);
    }
}
