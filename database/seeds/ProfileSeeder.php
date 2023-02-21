<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profiles')->delete();
        DB::table('profiles')->insert([
            [
                'name' => 'Administrateur',
                'slug' => 'admin'
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager'
            ],
            [
                'name' => 'Consultation',
                'slug' => 'consult'
            ]
        ]);
    }
}
