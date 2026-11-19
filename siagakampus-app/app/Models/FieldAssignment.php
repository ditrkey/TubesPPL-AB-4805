<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldAssignment extends Model
{
    use HasFactory;

    protected $table = 'field_assignments';

    protected $fillable = [
        'user_id',
        'report_id',
        'sos_alert_id',
        'inventory_id',
        'instruction',
        'status',
    ];

    /**
     * Field responder/staff assigned to this task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Report associated with this field assignment.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}

