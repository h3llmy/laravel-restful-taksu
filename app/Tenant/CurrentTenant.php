<?php

namespace Taksu\Tenant;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

final class CurrentTenant
{
    /**
     * Guards against re-entering Auth::user() while it is still resolving the
     * User model itself (e.g. EloquentUserProvider::retrieveById()), which would
     * otherwise recurse indefinitely under stateful/cookie auth. Uses Context
     * rather than a static property so the guard is request-scoped and safe
     * under Octane/Swoole, where a worker process outlives a single request.
     */
    private const RESOLVING_KEY = self::class.'.resolving';

    public static function id(): ?string
    {
        if (Context::get(self::RESOLVING_KEY) === true) {
            return null;
        }

        Context::add(self::RESOLVING_KEY, true);

        try {
            return Auth::user()?->tenant_id;
        } finally {
            Context::forget(self::RESOLVING_KEY);
        }
    }
}
