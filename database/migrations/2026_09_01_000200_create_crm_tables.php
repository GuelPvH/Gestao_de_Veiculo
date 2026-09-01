<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 20)->default('company')->index();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('document', 32)->nullable()->unique();
            $table->string('email')->nullable()->index();
            $table->string('phone', 32)->nullable();
            $table->json('address')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->index();
            $table->string('phone', 32)->nullable();
            $table->string('source', 80)->nullable()->index();
            $table->string('project_type', 120)->nullable()->index();
            $table->string('status', 30)->default('new')->index();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->date('desired_deadline')->nullable();
            $table->text('objective')->nullable();
            $table->text('notes')->nullable();
            $table->text('lost_reason')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2);
            $table->string('status', 30)->default('draft')->index();
            $table->date('valid_until')->nullable()->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('clients');
    }
};
