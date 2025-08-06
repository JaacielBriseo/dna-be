<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnaRecord extends Model
{
    protected $fillable = ['dna_sequence', 'hash', 'has_mutation'];
}
