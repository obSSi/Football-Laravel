<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Championnat extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    /**
     * Teams registered in this championship.
     */
    public function equipes()
    {
        return $this->hasMany(Equipe::class);
    }

    /**
     * Fixtures scheduled for this championship.
     */
    public function matchs()
    {
        return $this->hasMany(Fixture::class, 'championnat_id');
    }
}
