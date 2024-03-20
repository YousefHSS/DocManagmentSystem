<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\roles;
use App\Models\User;
use App\Models\user_roles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        make admin account
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'Admin@gmail.com',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
        ]);


//        make uploader account
        User::factory()->create([
            'name' => 'Uploader',
            'email' => 'uploader@gmail.com',
            'password' => bcrypt('password'),
        ]);

//        make reviewer account
        User::factory()->create([
            'name' => 'Reviewer',
            'email' => 'reviewer@gmail.com',
            'password' => bcrypt('password'),
        ]);
//        make finalizer account
        User::factory()->create([
            'name' => 'Finalizer',
            'email' => 'finalizer@gmail.com',
            'password' => bcrypt('password'),
        ]);




        roles::factory()->create([
            'role_name' => 'Admin',
            'role_description' => 'Admin',
            'role_slug' => 'admin',
        ]);

        roles::factory()->create([
            'role_name' => 'User',
            'role_description' => 'User',
            'role_slug' => 'user',
        ]);


        roles::factory()->create([
            'role_name' => 'Uploader',
            'role_description' => 'Uploader',
            'role_slug' => 'uploader',
        ]);

        roles::factory()->create([
            'role_name' => 'Reviewer',
            'role_description' => 'Reviewer',
            'role_slug' => 'reviewer',
        ]);

        roles::factory()->create([
            'role_name' => 'Finalizer',
            'role_description' => 'Finalizer',
            'role_slug' => 'finalizer',
        ]);



        user_roles::factory()->create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
        user_roles::factory()->create([
            'user_id' => 2,
            'role_id' => 2,
        ]);
        user_roles::factory()->create([
            'user_id' => 3,
            'role_id' => 3,
        ]);

        user_roles::factory()->create([
            'user_id' => 4,
            'role_id' => 4,
        ]);

        user_roles::factory()->create([
            'user_id' => 5,
            'role_id' => 5,
        ]);

//        make 10 users with user role
User::factory(10)->create()->each(function ($user) {
            $user->roles()->attach(2);
        });


//make 10 docs
        \App\Models\Document::factory(10)->create();


    }
}
