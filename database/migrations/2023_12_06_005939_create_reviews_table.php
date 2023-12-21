<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            #كده انا بعمل الجدول ده انه forign key
//            $table->unsignedBigInteger('book_id');

            $table->text('review');
            $table->unsignedTinyInteger('rating');

            $table->timestamps();

            #وده تكمله الكود بقوله خليلي ال book_id->foreign key . المرجع بتاعه ال id الي الجدول بتاعه اسمه books

//            $table->foreign('book_id')->references('id')->on('books')
//                ->onDelete('cascade');

            #ده امر تاني بنخلي الجدول ده foreign key اسهل من الامر ال فوقه
            $table->foreignId('book_id')->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
