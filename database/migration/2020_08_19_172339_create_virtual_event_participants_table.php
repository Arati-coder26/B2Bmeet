<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualEventParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_event_participants', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('userid');
            $table->enum('approval_status', ['Pending', 'Accepted','Rejected'])->default('Pending');
            $table->enum('status', ['Pending', 'In Progress','Completed'])->default('Pending');
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
        Schema::dropIfExists('virtual_event_participants');
    }
}
