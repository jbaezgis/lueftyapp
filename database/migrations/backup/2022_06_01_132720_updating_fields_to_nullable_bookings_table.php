<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatingFieldsToNullableBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('arrival_time')->nullable()->change();
            $table->decimal('catering', 6, 2)->nullable()->change();
            $table->decimal('fair', 6, 2)->nullable()->change();
            $table->decimal('extra_payment', 6, 2)->nullable()->change();
            $table->string('fullname', 150)->nullable()->change();
            $table->string('payment_method')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('arrival_time')->nullable(false)->change();
            $table->decimal('catering', 6, 2)->nullable(false)->change();
            $table->decimal('fair', 6, 2)->nullable(false)->change();
            $table->decimal('extra_payment', 6, 2)->nullable(false)->change();
            $table->string('fullname', 150)->nullable(false)->change();
            $table->string('payment_method')->nullable(false)->change();

        });
    }
}
