<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfp_id',
        'partner_id',
        'bid_amount',
        'proposal_file_path',
        'status',
    ];

    public function rfp()
    {
        return $this->belongsTo(Rfp::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
