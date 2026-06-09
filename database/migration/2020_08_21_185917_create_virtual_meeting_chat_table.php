<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualMeetingChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_meeting_chat', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->string('sender_id');
            $table->string('receiver_id');
            $table->mediumText('message');
            $table->enum('message_read_status', ['Unread', 'Read'])->default('Unread');
            $table->timestamps();
            $table->foreign('meeting_id')->references('meeting_id')->on('virtual_event_meetings')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtual_meeting_chat');
    }
}
