<?php

namespace Taksu\Concerns;

use App\Models\Scopes\TenantScope;
use App\Support\CurrentTenant;

trait TenantAware
{
    public static function bootTenantAware(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (($tenantId = CurrentTenant::id()) !== null) {
                $model->tenant_id = $tenantId;
            }
        });

        static::updating(function ($model) {
            if (CurrentTenant::id() !== null && $model->isDirty('tenant_id')) {
                $model->tenant_id = $model->getOriginal('tenant_id');
            }
        });

        // hide the tenant id.
        static::retrieved(fn ($model) => $model->makeHidden('tenant_id'));
    }
}
