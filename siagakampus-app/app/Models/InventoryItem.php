<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'atribut',
        'jumlah',
        'updated_by',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function usages()
    {
        return $this->hasMany(InventoryUsage::class);
    }
}
