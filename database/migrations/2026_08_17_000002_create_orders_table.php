<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('organization')->nullable();
            $table->string('project_type');
            $table->longText('details');
            $table->string('budget_range');
            $table->date('desired_deadline')->nullable();
            $table->string('contact_preference')->default('whatsapp');
            $table->string('status')->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->string('source')->default('website');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
