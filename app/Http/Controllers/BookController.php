<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input( 'filter' , '');
        $books = Book::When(
            $title ,
            fn($query , $title) => $query->title($title)
        );

        $books = match ($filter) {
            # ال ده ده هوا ال بتشغل ال (latest,PopularLastMonth ,PopularLast6Months ,HighestRatedLastMonth ,HighestRatedLast6Month)
            'popular_last_month' => $books->PopularLastMonth(),
            'popular_last_6months' => $books->PopularLast6Months(),
            'highest_last_month' => $books->HighestRatedLastMonth(),
            'highest_last_6months' => $books->HighestRatedLast6Months(),
            default => $books->latest()->WithAvgRating()->WithReviewsCount()
        };

//        $books = $books->get();

        #كل ال بيعمله ال cache بيخزن الداتا علشان لو في كذا حد طلبها ميقعدش يدور عليها ويجيبها لا ده هوا بيجيبها علطول
        # عملت ($cahcekey) وحطيت فيه ال books يعني هاتي ال $filter او هات ال title
        $cachekey = 'books:' . $filter . ':' . $title ;
        #الامر ده قولته ال var ال اسمه books حطلي جوا cache وهاتلي ال var ال انا عملته ال اسمه $cachekey , سواعملي cache لمده ساعه وبعدين عملت arrow fun وقولتله استعدي ال var ال انا عملتها ال اسمها $books وبعدين هاتها اعملي get() ليها يعني
        $books = $books->paginate(10);
//            cache()->remember(
////                $cachekey ,
////                  3600  ,
////                fn()  =>
//                $books->paginate(10);
//        $books = cache::remember('books' , 3600 ,fn()  => $books->get());

        return view('books.index' , ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        #الأمر ده هيخزن كل كتاب فردي لمده ساعه واحده
        $cachekey = 'book:' . $id ;

        $book = cache()->remember(
            $cachekey,
            3600 ,
            fn() =>
            Book::with([
                'reviews' => fn($query) => $query ->latest()
            ])->WithAvgRating()->WithReviewsCount()->findorFail($id)
        );

        return view('books.show' ,['book' => $book]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
