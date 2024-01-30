<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryFake extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $dataCate = [
            [
                "id"=>1,
                "name_category"=> "Dien thoai",
                "slug"=>"Dien thoai"
            ],
            [
                "id"=> 2,
                "name_category"=> "Dien thoai 1",
                "slug"=>"Dien thoai 1"
            ],
            [   
                "id"=> 3,
                "name_category"=> "Dien thoai 2",
                "slug"=>"Dien thoai 2"
            ],
        ];
        DB::table('table_category')->insert($dataCate);
    }
}
