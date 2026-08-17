<?php

declare(strict_types=1);

use App\Enums\VehicleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->string('plate', 8)->unique();
            $table->string('brand', 60);
            $table->string('model', 60);
            $table->unsignedSmallInteger('year');
            $table->string('status', 20)->default(VehicleStatus::Available->value);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
