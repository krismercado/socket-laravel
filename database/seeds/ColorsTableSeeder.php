<?php

use Illuminate\Database\Seeder;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('colors')->insert([
          'color' => 'red'
      ]);

      DB::table('colors')->insert([
          'color' => 'blue'
      ]);

      DB::table('colors')->insert([
          'color' => 'green'
      ]);
    }
}
