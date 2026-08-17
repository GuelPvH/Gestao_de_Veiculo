<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('retorna 200 quando DB e Redis estão acessíveis', function (): void {
    $response = get('/up/deep');

    $response->assertOk()
        ->assertJson([
            'app' => true,
            'db' => true,
            'redis' => true,
        ]);
});
