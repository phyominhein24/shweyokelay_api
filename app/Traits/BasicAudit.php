<?php

namespace App\Traits;

trait BasicAudit
{
    protected static function bootBasicAudit()
    {
        $self = new static();

        if (auth()->id()) {
            $userId = auth()->id();
        } else {
            $userId = null;
        }

        static::creating(function ($model) use ($userId) {
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });

        static::updating(function ($model) use ($userId) {
            $model->updated_by = $userId;
        });

        static::deleting(function ($model) use ($self, $userId) {
            if ($self->isSoftDeleteEnabled()) {
                $model->deleted_by = $userId;
                $model->save();
            }
        });
    }

    public function isSoftDeleteEnabled()
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this)) && ! $this->forceDeleting;
    }
}
