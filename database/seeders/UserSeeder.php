<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            $data = [
                [   
                    'name'=> "vanhanh",
                    'email' => 'levanhanh@gmail.com',
                    'password'=> bcrypt('123456789'),
                    'level'=> 1
                ], [
                    'name'=> "vanhanh1",
                    'email' => 'admin@gmail.com',
                    'password'=> bcrypt('123456789'),
                    'level'=> 1
                ]
                ];
            DB::table('table_user')->insert($data);
    }
}