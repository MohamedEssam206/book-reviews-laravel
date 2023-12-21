<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

     #عندك 3 اكواد كل كود كل كود بيشير ل حاجه
//    1- good review
//    2- average review
//    3- bad review


    public function run(): void
    {
        #الاول ضاف 33 كتاب
        Book::factory(33)->create()->each(function ($book) {
            $numReviews = random_int(5 , 30);

            Review::factory()->count($numReviews)
            ->good()
            ->for($book)
            ->create();
        });

        #الثاني ضاف 34 كتاب
        Book::factory(34)->create()->each(function ($book) {
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
            ->average()
            ->for($book)
            ->create();
        });
#الثالث ضاف 33 كتاب
        Book::factory(33)->create()->each(function ($book) {
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
            ->bad()
            ->for($book)
            ->create();
        });
    }
}
