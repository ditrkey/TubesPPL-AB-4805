<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zones';

    protected $fillable = [
        'name',
        'description',
        'risk_level',
        'boundary',
    ];

    /**
     * Reports associated with this flood risk zone.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'zone_id');
    }

    /**
     * Get badge color representation for risk level.
     */
    public function getRiskBadgeAttribute(): array
    {
        return match (strtolower($this->risk_level ?? 'rendah')) {
            'tinggi' => [
                'label' => 'Tinggi',
                'class' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-300',
            ],
            'sedang' => [
                'label' => 'Sedang',
                'class' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300',
            ],
            default => [
                'label' => 'Rendah',
                'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300',
            ],
        };
    }
}

