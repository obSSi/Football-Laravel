<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'championnat_id'];

    public function championnat()
    {
        return $this->belongsTo(Championnat::class);
    }

    public function homeMatchs()
    {
        return $this->hasMany(Fixture::class, 'equipe1_id');
    }

    public function awayMatchs()
    {
        return $this->hasMany(Fixture::class, 'equipe2_id');
    }
}
