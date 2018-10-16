<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
      // Let's truncate our existing records to start from scratch.
      Product::truncate();

      $faker = \Faker\Factory::create();

      // And now, let's create a few articles in our database:
      for ($i = 0; $i < 5; $i++) {
          Product::create([
              'cathegory' => 'Ski',
              'type' => 'Batons',
          ]);
      }
      for ($i = 0; $i < 2; $i++) {
        Product::create([
            'cathegory' => 'Ski',
            'type' => 'Chaussures',
        ]);
      }     
      for ($i = 0; $i < 2; $i++) {
        Product::create([
            'cathegory' => 'Randonee',
            'type' => 'Batons',
        ]);
      }
      for ($i = 0; $i < 1; $i++) {
        Product::create([
          'cathegory' => 'Randonee',
          'type' => 'Chaussures',
        ]);
      } 
    }
}
