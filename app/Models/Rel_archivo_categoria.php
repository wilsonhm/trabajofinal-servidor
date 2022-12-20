<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rel_archivo_categoria extends Model
{
    use HasFactory;
    protected $table = 'rel_archivos_categorias';
    protected $fillable = [
        'archivo_id',
        'categoria_id'
    ];
}
