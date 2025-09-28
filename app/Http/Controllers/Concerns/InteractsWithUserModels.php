<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait InteractsWithUserModels
{
    protected function guardUserModel(Model $model, Request $request): void
    {
        if ($model->getAttribute('user_id') !== $request->user()->id) {
            throw new AccessDeniedHttpException();
        }
    }
}
