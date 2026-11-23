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
        Schema::table('pedidos', function (Blueprint $table) {
            if (! Schema::hasColumn('pedidos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('pedidos', 'status')) {
                $table->string('status')->default('aberto');
            }

            if (! Schema::hasColumn('pedidos', 'total')) {
                $table->decimal('total', 10, 2)->default(0);
            }

            if (! Schema::hasColumn('pedidos', 'observacoes')) {
                $table->text('observacoes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'status', 'total', 'observacoes']);
        });
    }
};
