<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
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
     * Championship this match belongs to.
     */
    public function championnat()
    {
        return $this->belongsTo(Championnat::class);
    }

    /**
     * Home team relation.
     */
    public function maison()
    {
        return $this->belongsTo(Equipe::class, 'equipe1_id');
    }

    /**
     * Away team relation.
     */
    public function exterieur()
    {
        return $this->belongsTo(Equipe::class, 'equipe2_id');
    }

    /**
     * Backward-compatible aliases used in existing code.
     */
    public function equipe1()
    {
        return $this->maison();
    }

    public function equipe2()
    {
        return $this->exterieur();
    }

    /**
     * Virtual score attribute formatted as "x - y".
     */
    protected function score(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->score1 === null || $this->score2 === null) {
                    return null;
                }

                return $this->score1 . ' - ' . $this->score2;
            },
        );
    }
}
