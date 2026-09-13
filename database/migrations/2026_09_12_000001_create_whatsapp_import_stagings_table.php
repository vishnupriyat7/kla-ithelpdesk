<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_import_stagings')) {
            Schema::create('whatsapp_import_stagings', function (Blueprint $table) {
            $table->id();

            // Mirrors complaint_tickets columns exactly, so review/edit maps 1:1 to the final row.
            $table->string('ticket_no')->nullable()->index();
            $table->string('employee_id')->nullable();
            $table->string('section')->nullable();
            $table->unsignedBigInteger('office_location_id')->nullable();
            $table->string('floor')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('complaint_type')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('Open');
            $table->text('remarks')->nullable();
            $table->string('vendor_complaint_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('reported_at')->nullable();

            // Import bookkeeping / audit fields, not part of complaint_tickets.
            $table->string('source_key')->nullable()->index();
            $table->text('raw_messages')->nullable();
            $table->unsignedInteger('merge_count')->default(1);
            $table->boolean('already_imported')->default(false);
            $table->unsignedBigInteger('imported_ticket_id')->nullable();
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_import_stagings');
    }
};
