<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class SellItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cannot_submit_item_without_required_fields()
    {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/sell', []);

    $response->assertSessionHasErrors([
        'item_name',
        'price',
        'description',
        'status',
        'item_image',
        'categories'  // 中間テーブル経由なので注意
    ]);
    }
}
