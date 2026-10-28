<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_key',
        'customer_id',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'channel',
        'assigned_to_id',
        'sla_deadline',
        'response_deadline',
        'first_responded_at',
        'resolved_at',
        'is_sla_breached',
        'sla_policy_id',
    ];

    protected $casts = [
        'sla_deadline' => 'datetime',
        'response_deadline' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_sla_breached' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function slaPolicy()
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    public function isResponseBreached(): bool
    {
        if ($this->first_responded_at) {
            return $this->response_deadline ? $this->first_responded_at->gt($this->response_deadline) : false;
        }
        return $this->response_deadline ? \Carbon\Carbon::now()->gt($this->response_deadline) : false;
    }

    public function isResolutionBreached(): bool
    {
        if (in_array($this->status, ['Resolved', 'Closed'])) {
            if ($this->resolved_at) {
                return $this->sla_deadline ? $this->resolved_at->gt($this->sla_deadline) : false;
            }
            return false;
        }
        return $this->sla_deadline ? \Carbon\Carbon::now()->gt($this->sla_deadline) : false;
    }


    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }
}
