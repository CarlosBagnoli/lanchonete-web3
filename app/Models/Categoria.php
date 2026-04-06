<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Categoria extends Model
{
    use HasFactory;
    protected $table = 'categorias';
    protected $fillable = [
        'nome',
        'descricao',
        'ativa',
    ];
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'categoria_id');
    }
}