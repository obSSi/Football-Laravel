<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Championnat extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function equipes()
    {
        return $this->hasMany(Equipe::class);
    }

    public function matchs()
    {
        return $this->hasMany(Fixture::class, 'championnat_id');
    }
}
