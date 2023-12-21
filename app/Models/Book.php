<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;


    #امر hasmany ده بيشير ان كل كتاب ليه اراء كتير بس كل كتاب ليه راي واحد يعني one to manys
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    #الفانكشن دي عملناها علشان توفر علينا كتابه الكود في ال tinker يعني كان الاول الكود كان كده
    #   \App\Models\Book::where('title' ,'LIKE' , '%ratione%')->get();

    #وبعدين بقا كده
    #   \App\Models\Book::title('vel')->get();
    #   \App\Models\Book::title('vel')->where('created_at' , '>' , 'وتحط التاريخ ')->get();



    #كلمه scope بيجي وراها علطول اول حرف لازم يكون كابتل زي Title كده
    public function scopeTitle(Builder $query , string $title): Builder
    {
        return $query->where('title' ,'LIKE' , '%' . $title . '%' );
    }

    public function scopeWithReviewsCount(Builder $query , $from = null , $to = null):Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q , $from , $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query , $from = null , $to = null):Builder|QueryBuilder
    {
        return $query->withavg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q , $from , $to)
        ] , 'rating');
    }

    #الامر ده يعني رجعلي ال Popular بتاع ال ('reviews_count' ) . desc يعني من الكبير الي الصغير
    public function scopePopular(Builder $query , $from = null , $to = null ):Builder|QueryBuilder
    {
        return $query->WithReviewsCount()
            ->orderBy('reviews_count' , 'desc');
    }


    #الامر ده يعني رجعلي ال highestrated بتاع ال (reviews ,rating )
    public function scopeHighestRated(Builder $query , $from = null , $to = null):Builder|QueryBuilder
    {
        return $query->WithAvgRating()
            ->orderBy('reviews_avg_rating' , 'desc');
    }

    public function scopeMinReviews(Builder $query , int $minReviews):Builder|QueryBuilder{
        return $query->having('reviews_count' , '>=' , $minReviews);
    }



    private function dateRangeFilter(Builder $query , $from = null , $to = null)
    {
        if ($from && !$to){
            $query->where('created_at' , '>=' , $from);
        } elseif (!$from && $to ){
            $query->where('created_at' ,'<=' , $to);
        } elseif ($from && $to){
            $query->whereBetween('created_at' , [$from , $to]);
        }
    }
    # ال functions دي هيا ال بتشغل ال (latest,PopularLastMonth ,PopularLast6Months ,HighestRatedLastMonth ,HighestRatedLast6Month)
    public function scopePopularLastMonth(Builder $query):Builder|QueryBuilder{
        return $query->popular(now()->subMonth() , now())
            ->highestRated(now()->subMonth() , now())
            ->minReviews(2);
    }
    public function scopePopularLast6Months(Builder $query):Builder|QueryBuilder{
        return $query->popular(now()->subMonth(6) , now())
            ->highestRated(now()->subMonth(6) , now())
            ->minReviews(5);
    }
    public function scopeHighestRatedLastMonth(Builder $query):Builder|QueryBuilder{
        return $query->HighestRated(now()->subMonth() , now())
            ->popular(now()->subMonth() , now())
            ->minReviews(2);
    }
    public function scopeHighestRatedLast6Months(Builder $query):Builder|QueryBuilder{
        return $query->HighestRated(now()->subMonth(6) , now())
            ->popular(now()->subMonth(6) , now())
            ->minReviews(5);
    }

    protected static function booted()
    {
        static::updated(
            fn (Book $Book) => cache()->forget('book:' . $Book->id)
        );
        static::deleted(
            fn (Book $Book) => cache()->forget('book:' . $Book->id)
        );

        static::created
        (fn (book $book) => cache()->forget('book:' . $book->book_id));
    }


}

