<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
           $username = 'user_' . $i;
           $user = User::query()->where('name', $username)->first();
           if (!$user) {
               $user =  new User();
           }
           $user->name = $username;
           $user->save();
        }
    }
}
