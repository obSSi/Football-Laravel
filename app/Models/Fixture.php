<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    protected $table = 'matchs';

    protected $fillable = [
        'championnat_id',
        'equipe1_id',
        'equipe2_id',
        'score1',
        'score2',
    ];

    /**
     * Championship this fixture belongs to.
     */
    public function championnat()
    {
        return $this->belongsTo(Championnat::class);
    }

    /**
     * Home team relation.
     */
    public function equipe1()
    {
        return $this->belongsTo(Equipe::class, 'equipe1_id');
    }

    /**
     * Away team relation.
     */
    public function equipe2()
    {
        return $this->belongsTo(Equipe::class, 'equipe2_id');
    }

    /**
     * Determine if fixture has both scores.
     */
    public function isPlayed(): bool
    {
        return !is_null($this->score1) && !is_null($this->score2);
    }
}
