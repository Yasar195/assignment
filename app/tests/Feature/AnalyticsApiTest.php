<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_browser_events(): void
    {
        $response = $this->postJson('/api/events', [
            'event_name' => 'page_view',
            'page' => '/restaurant',
            'source' => 'browser',
            'session_id' => 'sess_123',
            'user_id' => 'user_456',
            'metadata' => [
                'title' => 'Sunset Grill',
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('event.event_name', 'page_view');
    }

    public function test_it_returns_dashboard_summary(): void
    {
        $this->postJson('/api/events', [
            'event_name' => 'page_view',
            'page' => '/restaurant',
            'session_id' => 'sess_123',
            'metadata' => ['title' => 'Sunset Grill'],
        ]);

        $this->postJson('/api/events', [
            'event_name' => 'order_created',
            'page' => '/restaurant',
            'session_id' => 'sess_123',
            'metadata' => ['item' => 'Grilled Salmon'],
        ]);

        $response = $this->getJson('/api/analytics/dashboard?start=2026-10-01&end=2026-10-31');

        $response->assertStatus(200)
            ->assertJsonPath('summary.page_views', 1)
            ->assertJsonPath('summary.total_events', 2);
    }
}
