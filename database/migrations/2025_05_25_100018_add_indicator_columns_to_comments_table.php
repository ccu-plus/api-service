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
        Schema::table('comments', function (Blueprint $table) {
            $table->tinyInteger('recommended')
                ->default(0)
                ->comment('推薦程度')
                ->after('content');

            $table->tinyInteger('informative')
                ->default(0)
                ->comment('知識性')
                ->after('recommended');

            $table->tinyInteger('challenging')
                ->default(0)
                ->comment('課程難度')
                ->after('informative');

            $table->tinyInteger('overall')
                ->default(0)
                ->comment('總體評分')
                ->after('challenging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['recommended', 'informative', 'challenging', 'overall']);
        });
    }
};
