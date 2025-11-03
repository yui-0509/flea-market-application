<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::where('email', 'user1@test.com')->first();
        $user2 = User::where('email', 'user2@test.com')->first();

        if (! $user1 || ! $user2) {
            throw new \RuntimeException('Users not found. Run UsersTableSeeder first.');
        }

        $items = [
            [
                'user_id' => $user1->id,
                'item_name' => '腕時計',
                'brand_id' => 1,
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'status' => 1,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'is_sold' => false,
                'category_ids' => [1, 5, 12],
            ],
            [
                'user_id' => $user1->id,
                'item_name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'status' => 2,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'is_sold' => false,
                'category_ids' => [2],
            ],
            [
                'user_id' => $user1->id,
                'item_name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'status' => 3,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'is_sold' => false,
                'category_ids' => [10],
            ],
            [
                'user_id' => $user1->id,
                'item_name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'status' => 4,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'is_sold' => false,
                'category_ids' => [1, 5],
            ],
            [
                'user_id' => $user1->id,
                'item_name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'status' => 1,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'is_sold' => false,
                'category_ids' => [2],
            ],
            [
                'user_id' => $user2->id,
                'item_name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'status' => 2,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'is_sold' => false,
                'category_ids' => [2],
            ],
            [
                'user_id' => $user2->id,
                'item_name' => 'ショルダーバッグ',
                'brand_id' => 2,
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'status' => 3,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'is_sold' => false,
                'category_ids' => [1, 4],
            ],
            [
                'user_id' => $user2->id,
                'item_name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'status' => 4,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'is_sold' => false,
                'category_ids' => [3],
            ],
            [
                'user_id' => $user2->id,
                'item_name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'status' => 1,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'is_sold' => false,
                'category_ids' => [3, 10],
            ],
            [
                'user_id' => $user2->id,
                'item_name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'status' => 2,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'is_sold' => false,
                'category_ids' => [4, 6],
            ]];
        foreach ($items as $itemData) {
            $categoryIds = $itemData['category_ids'] ?? [];
            unset($itemData['category_ids']);

            $item = Item::create($itemData);

            if (! empty($categoryIds)) {
                $item->categories()->attach($categoryIds);
            }
        }
    }
}
