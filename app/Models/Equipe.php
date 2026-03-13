<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'championnat_id'];

    /**
     * Championship this team belongs to.
     */
    public function championnat()
    {
        return $this->belongsTo(Championnat::class);
    }

    /**
     * Fixtures where this team is home side.
     */
    public function homeMatchs()
    {
        return $this->hasMany(Fixture::class, 'equipe1_id');
    }

    /**
     * Fixtures where this team is away side.
     */
    public function awayMatchs()
    {
        return $this->hasMany(Fixture::class, 'equipe2_id');
    }
}
