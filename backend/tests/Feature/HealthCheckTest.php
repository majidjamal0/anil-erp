<?php

it('reports API and database readiness', function (): void {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'service' => 'anil-erp-api',
        ])
        ->assertJsonStructure(['timestamp']);
});
