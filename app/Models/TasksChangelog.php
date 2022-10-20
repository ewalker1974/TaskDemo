<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TasksChangelog extends BaseModel
{
    protected $casts = [
        'changes' => 'string',
    ];

    /**
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->uid,
            'task_id' => $this->task->uid,
            'changes' => json_decode($this->getAttribute('changes')),
            'created_at' => $this->created_at,
        ];
    }
}
