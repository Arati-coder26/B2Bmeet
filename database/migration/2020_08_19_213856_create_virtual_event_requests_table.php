<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualEventRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_event_requests', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('request_id')->unique();
            $table->string('participant1_userid');
            $table->string('participant2_userid');
            $table->string('intro_message')->nullable();
            $table->string('parent_request_id')->nullable();
            $table->enum('approval_status', ['Pending', 'Accepted','Rejected','Rescheduled'])->default('Pending');
            $table->dateTime('from_date_time');
            $table->dateTime('to_date_time');
            $table->timestamps();
            $table->foreign('event_id')->references('event_id')->on('virtual_events')->constrained()->onUpdate('cascade')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtual_event_requests');
    }
}
