<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $guarded = ['deleted_at'];

    protected $fillable = [
        'name',
        'user_id',
        'categoria'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function archivo()
    {
        return $this->belongsToMany(Archivo::class, 'rel_archivos_categorias', 'archivo_id', 'categoria_id');
    }
}
