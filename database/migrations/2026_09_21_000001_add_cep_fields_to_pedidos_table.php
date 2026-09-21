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
            if (! Schema::hasColumn('pedidos', 'cep')) {
                $table->string('cep', 8)->nullable()->after('observacoes');
            }

            if (! Schema::hasColumn('pedidos', 'logradouro')) {
                $table->string('logradouro')->nullable()->after('cep');
            }

            if (! Schema::hasColumn('pedidos', 'bairro')) {
                $table->string('bairro')->nullable()->after('logradouro');
            }

            if (! Schema::hasColumn('pedidos', 'cidade')) {
                $table->string('cidade')->nullable()->after('bairro');
            }

            if (! Schema::hasColumn('pedidos', 'uf')) {
                $table->string('uf', 2)->nullable()->after('cidade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['cep', 'logradouro', 'bairro', 'cidade', 'uf']);
        });
    }
};
