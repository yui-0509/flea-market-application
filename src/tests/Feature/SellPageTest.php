<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_sell_page_can_be_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/sell');

        $response->assertStatus(200);
    }

    public function test_sell_page_redirects_if_not_authenticated(): void
    {
        $response = $this->get('/sell');

        $response->assertRedirect('/login');
    }

    //public function test_user_can_sell_item_successfully()
    //{

    //   Storage::fake('public');

    //   $this->seed(\Database\Seeders\CategoriesTableSeeder::class);

    //    $user = User::factory()->create();
    //    $category = Category::first();

    //    $response = $this->actingAs($user)->post('/sell', [
    //        'item_name' => 'テスト商品',
    //        'price' => 1000,
    //        'description' => 'これはテスト説明です',
    //        'status' => 1,
    //        'item_image' => UploadedFile::fake()->image('item.jpg'),
    //        'categories' => [$category->id],
    //    ]);

    //    $this->assertDatabaseHas('items', [
    //        'item_name' => 'テスト商品',
    //        'price' => 1000,
    //        'description' => 'これはテスト説明です',
    //        'status' => 1,
    //    ]);

    //    Storage::disk('public')->assertDirectoryExists('item_images');

    //    $this->assertDatabaseCount('item_category', 1);

    //    $response->assertRedirect('/');
    //}

    public function test_sell_item_validation_errors(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/sell/store', []);

        $response->assertSessionHasErrors([
            'item_name',
            'price',
            'description',
            'status',
            'item_image',
            'categories',
        ]);
    }
}
