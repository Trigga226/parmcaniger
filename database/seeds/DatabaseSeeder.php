<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProfileSeeder::class);
    }
}
