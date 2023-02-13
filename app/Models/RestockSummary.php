<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockSummary extends Model
{
    use HasFactory;
    protected $guarded;

    public function restock_items()
    {
        return $this->hasMany(Restock::class, 'summary_id');
    }

    public function item()
    {
        return $this->hasOneThrough(item::class, Restock::class, 'summary_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
