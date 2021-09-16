<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ver8以降の「DB::」コマンドに必要
use Illuminate\Support\Facades\Hash; // ver8以降の「Hash::」コマンドに必要

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $userName = [
            'master',
            '有光勇毅',
            '江崎光',
            '河津翔太',
            '鍬崎颯太',
            '田辺元気',
            '三浦敦志'
        ];
        $userEmail = [
            '1801007z@gmail.com',
            '1801002@s.asojuku.ac.jp',
            '1801007@s.asojuku.ac.jp',
            '1801012@s.asojuku.ac.jp',
            '1801016@s.asojuku.ac.jp',
            '1901023@s.asojuku.ac.jp',
            '1801030@s.asojuku.ac.jp'
        ];

        for($i = 0; $i < count($userName); $i++){
            DB::table('users')->insert([
                'name' => $userName[$i],
                'email' => $userEmail[$i],
                'password' => Hash::make('00000000'),
                'created_at' => '2021-09-01 00:09:00',
                'updated_at' => '2021-09-01 00:09:00'
            ]);
        }
    }
}
