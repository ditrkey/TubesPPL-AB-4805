<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reports';

    protected $fillable = [
        'user_id',
        'zone_id',
        'title',
        'category',
        'water_level',
        'description',
        'photo_path',
        'location',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * User / reporter who submitted the incident.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Flood risk zone associated with the report.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    /**
     * Field responder assignment for this incident.
     */
    public function fieldAssignment(): HasOne
    {
        return $this->hasOne(FieldAssignment::class, 'report_id');
    }

    /**
     * Report formatted code identifier (LP-XXXX).
     */
    public function getCodeAttribute(): string
    {
        return sprintf('LP-%04d', $this->id);
    }

    /**
     * Public URL for the incident photo.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo_path)) {
            return asset('images/report-placeholder.jpg');
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return Storage::disk('public')->url($this->photo_path);
    }

    /**
     * Extract latitude coordinate from spatial attribute.
     */
    public function getLatitudeAttribute($value): ?float
    {
        if (isset($this->attributes['latitude']) && is_numeric($this->attributes['latitude'])) {
            return (float) $this->attributes['latitude'];
        }

        if (! empty($this->attributes['location'])) {
            $raw = $this->attributes['location'];

            // If binary POINT string from MySQL (minimum 25 bytes: 4 srid + 1 order + 4 type + 8 x + 8 y)
            if (is_string($raw) && strlen($raw) >= 25 && ! str_contains($raw, 'POINT')) {
                $coords = unpack('d2', substr($raw, 9));
                if ($coords && isset($coords[2])) {
                    return (float) $coords[2];
                }
            }

            // If WKT string format: POINT(lng lat)
            if (is_string($raw) && preg_match('/POINT\s*\(\s*([-\d\.]+)\s+([-\d\.]+)\s*\)/i', $raw, $matches)) {
                return (float) $matches[2];
            }
        }

        return null;
    }

    /**
     * Extract longitude coordinate from spatial attribute.
     */
    public function getLongitudeAttribute($value): ?float
    {
        if (isset($this->attributes['longitude']) && is_numeric($this->attributes['longitude'])) {
            return (float) $this->attributes['longitude'];
        }

        if (! empty($this->attributes['location'])) {
            $raw = $this->attributes['location'];

            // If binary POINT string from MySQL
            if (is_string($raw) && strlen($raw) >= 25 && ! str_contains($raw, 'POINT')) {
                $coords = unpack('d2', substr($raw, 9));
                if ($coords && isset($coords[1])) {
                    return (float) $coords[1];
                }
            }

            // If WKT string format: POINT(lng lat)
            if (is_string($raw) && preg_match('/POINT\s*\(\s*([-\d\.]+)\s+([-\d\.]+)\s*\)/i', $raw, $matches)) {
                return (float) $matches[1];
            }
        }

        return null;
    }

    /**
     * Water level visual styling and description.
     */
    public function getWaterLevelInfoAttribute(): array
    {
        return match ($this->water_level) {
            'High' => [
                'code' => 'High',
                'label' => 'Tinggi (>40 cm)',
                'short' => '>40 cm',
                'badge_class' => 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
                'dot_class' => 'bg-rose-500',
            ],
            'Med' => [
                'code' => 'Med',
                'label' => 'Sedang (15-40 cm)',
                'short' => '15-40 cm',
                'badge_class' => 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
                'dot_class' => 'bg-amber-500',
            ],
            default => [
                'code' => 'Low',
                'label' => 'Rendah (<15 cm)',
                'short' => '<15 cm',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                'dot_class' => 'bg-emerald-500',
            ],
        };
    }

    /**
     * Status badge styling with requested distinct colors:
     * - Pending: Amber / Kuning
     * - Verified: Cyan / Biru
     * - Handling: Indigo / Ungu
     * - Resolved: Emerald / Hijau
     * - Rejected: Rose / Merah
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Pending' => [
                'label' => 'Menunggu Verifikasi',
                'pill' => 'Pending',
                'badge_class' => 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-700/60',
                'dot_class' => 'bg-amber-500 animate-pulse',
            ],
            'Verified' => [
                'label' => 'Terverifikasi',
                'pill' => 'Verified',
                'badge_class' => 'bg-sky-100 text-sky-800 border border-sky-300 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-700/60',
                'dot_class' => 'bg-sky-500',
            ],
            'Handling' => [
                'label' => 'Sedang Ditangani',
                'pill' => 'Handling',
                'badge_class' => 'bg-indigo-100 text-indigo-800 border border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-700/60',
                'dot_class' => 'bg-indigo-500',
            ],
            'Resolved' => [
                'label' => 'Selesai',
                'pill' => 'Resolved',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-700/60',
                'dot_class' => 'bg-emerald-500',
            ],
            'Rejected' => [
                'label' => 'Ditolak',
                'pill' => 'Rejected',
                'badge_class' => 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-700/60',
                'dot_class' => 'bg-rose-500',
            ],
            default => [
                'label' => $this->status ?? 'Unknown',
                'pill' => $this->status ?? 'Unknown',
                'badge_class' => 'bg-gray-100 text-gray-800 border border-gray-300 dark:bg-gray-800 dark:text-gray-300',
                'dot_class' => 'bg-gray-400',
            ],
        };
    }

    /**
     * Query scope to include latitude and longitude extracted from MySQL spatial column.
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            return $query->select('reports.*')
                ->selectRaw('ST_X(location) as longitude, ST_Y(location) as latitude');
        }

        return $query;
    }

    /**
     * Query scope for search and filtering.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Search term
        if (! empty($filters['q'])) {
            $search = trim($filters['q']);

            // If user typed LP-0012 or similar format, extract numeric ID
            if (preg_match('/^LP-?(\d+)$/i', $search, $matches)) {
                $numericId = (int) $matches[1];
                $query->where(function ($q) use ($numericId, $search) {
                    $q->where('reports.id', $numericId)
                        ->orWhere('reports.title', 'like', "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('reports.title', 'like', "%{$search}%")
                        ->orWhere('reports.description', 'like', "%{$search}%")
                        ->orWhere('reports.category', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('zone', function ($zq) use ($search) {
                            $zq->where('name', 'like', "%{$search}%");
                        });
                });
            }
        }

        // Status filter
        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('reports.status', $filters['status']);
        }

        // Category filter
        if (! empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('reports.category', $filters['category']);
        }

        // Date filter
        if (! empty($filters['date'])) {
            $query->whereDate('reports.created_at', $filters['date']);
        }

        return $query;
    }
}

