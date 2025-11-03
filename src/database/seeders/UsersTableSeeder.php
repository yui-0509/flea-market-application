<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username' => '一般ユーザー１',
                'email' => 'user1@test.com',
                'password' => Hash::make('password123'),
            ],
            [
                'username' => '一般ユーザー２',
                'email' => 'user2@test.com',
                'password' => Hash::make('password123'),
            ],
            [
                'username' => '一般ユーザー３',
                'email' => 'user3@test.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $user) {
            $userModel = User::create(array_merge($user, [
                'email_verified_at' => now(),
            ]));

            Profile::create([
                'user_id' => $userModel->id,
                'post_code' => '123-456'.$userModel->id,
                'address' => '東京都渋谷区'.$userModel->id.'丁目',
            ]);
        }
    }
}
