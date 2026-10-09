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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('target_role', 50)->nullable(); // 'client', 'pm', 'pmh', 'presales', 'finance', 'admin', 'all'
            $table->foreignId('change_request_id')->nullable()->constrained('change_requests')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type', 30)->default('info'); // 'danger', 'warning', 'success', 'info', 'primary'
            $table->string('category', 50)->default('system'); // 'cr_rejection', 'cr_approval', 'cr_submitted', 'status_update', 'quotation', 'invoice', 'deadline', 'system'
            $table->string('action_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['target_role', 'is_read']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
