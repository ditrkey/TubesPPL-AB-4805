<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_locations';

    // 11 Atribut Resmi Antisipasi Bencana
    public const ATRIBUT_LIST = [
        'Perahu Karet',
        'Kotak P3K',
        'Pelampung/Life Jacket',
        'Dayung',
        'Obat-obatan',
        'Perban',
        'Betadine',
        'Plaster',
        'Masker',
        'Air Mineral',
        'Selimut',
    ];

    protected $fillable = [
        'user_id',
        'lokasi',
        'location_type',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function usages()
    {
        return $this->hasMany(InventoryUsage::class);
    }
}
