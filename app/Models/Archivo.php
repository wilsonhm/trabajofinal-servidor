<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['deleted_at','user_id'];
    protected $table = 'archivos';

    protected $fillable = [
        'name',
        'description',
        'file_date',
        'file_name',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsToMany(Categoria::class, 'rel_archivos_categorias', 'archivo_id', 'categoria_id');
    }
}
