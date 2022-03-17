<?php

namespace Jeffleyd\EsLikeEloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Jeffleyd\ESLikeEloquent\EsQuery;

class EsModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $this->esQuery()->create($model->getAttributes());
        });

        static::updated(function($model) {
            $this->esQuery()->update($model->id, $model->getAttributes());
        });

        static::deleting(function($model) {
            $this->esQuery()->delete($model->id);
        });
    }

    public function esQuery(): EsQuery
    {
        return (new EsQuery($this->table));
    }
}
