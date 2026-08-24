<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    public const STATUSES = [
        'aberto',
        'em preparo',
        'pronto',
        'entregue',
        'fechado',
    ];

    protected $table = 'pedidos';

    protected $fillable = [
        'user_id',
        'status',
        'total',
        'observacoes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    public function isClosed(): bool
    {
        return $this->status === 'fechado';
    }

    public function canEdit(): bool
    {
        return ! $this->isClosed();
    }

    public function nextStatus(): ?string
    {
        $currentIndex = array_search($this->status, self::STATUSES, true);

        if ($currentIndex === false || $currentIndex >= count(self::STATUSES) - 1) {
            return null;
        }

        return self::STATUSES[$currentIndex + 1];
    }
}
