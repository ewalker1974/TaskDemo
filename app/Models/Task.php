<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends BaseModel
{
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->uid,
            'name' => $this->name,
            'due_to' => $this->due_to,
            'status' => $this->status,
            'assigned_user_id' => $this->user->uid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }
}
