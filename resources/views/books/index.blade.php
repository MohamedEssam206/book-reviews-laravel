@extends('layouts.app')

@section('content')

    <h1 class="mb-10 text-3xl ">Books</h1>
{{--الفورم دي الي بتعمل ال search--}}
    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex items-center space-x-2">
        <input type="text" name="title" placeholder="Search By Title" value="{{ request('title') }}" class="input h-10 " />
        <input type="hidden" name="filter" value="{{ request('filter') }}" />
        <button type="submit" class="btn h-10 ">Search</button>
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
    </form>


    <div class="filter-container mb-4 flex">
        @php
        $filters = [
        '' => 'Leatest',
        'popular_last_month' => 'Popular Last Month',
        'popular_last_6months' => 'Popular Last 6 Months',
        'highest_last_month' => 'Highest Last Month',
        'highest_last_6months' => 'Highest Last 6 Months',
        ];
        @endphp
        {{-- الامر ده بياخد ال variable ال فوق ال هوا filters ويحطها ك $key ويخزنها في ال label  --}}
        @foreach($filters as $key => $label)
            {{--...request()->query()
             الامر ده لما يكون في حاجه في ال search وتضغط علي اي قايمه تانيه ال data مش بتتمسح
             --}}
            <a href="{{ route('books.index' , [...request()->query() , 'filter' => $key]) }}"
               class="{{request('filter') === $key || (request('filter') === null && $key === '') ? 'filter-item-active' : 'filter-item'}}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <ul>
        @forelse($books as $book )
            <li class="mb-4">
                <div class="book-item">
                    <div
                        class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{ route('books.show' , $book) }}" class="book-title">{{$book->title}}</a>
                            <span class="book-author">by {{ $book->author }}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                <x-star-rating  :rating="$book->reviews_avg_rating"/>
                            </div>
                            <div class="book-review-count">
                               From {{ $book->reviews_count }} {{ Str::plural('Review' , $book->reviews_count) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No Books Found</p>
                    <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
        @if($books->count())
            <nav>
                {{ $books->links() }}
            </nav>
        @endif
    </ul>
@endsection
