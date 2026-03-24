<?php

namespace App\Models;

class Fixture extends FootballMatch
{
    /**
     * Determine if fixture has both scores.
     */
    public function isPlayed(): bool
    {
        return !is_null($this->score1) && !is_null($this->score2);
    }
}
