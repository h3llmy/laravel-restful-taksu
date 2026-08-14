<?php

namespace Taksu\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Taksu\Restful\Traits\ModelCommonTrait;

class Tenant extends Model
{
    use HasUlids;
    use ModelCommonTrait;

    protected $fillable = [
        'name',
        'domain',
        'slug',
        'settings',
        'state',
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }
}
