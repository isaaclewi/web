<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InstitutionScopeGlobal implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (app()->bound('current_institution_id')) {
            $builder->where(
                $model->getTable() . '.institution_id',
                app('current_institution_id')
            );
        }
    }
}
