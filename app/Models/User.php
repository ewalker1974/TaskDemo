<?php

namespace App\Models;

class User extends BaseModel
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->uid,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
