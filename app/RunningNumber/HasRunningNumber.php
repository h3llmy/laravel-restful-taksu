<?php

namespace Taksu\RunningNumber;

use Illuminate\Support\Facades\DB;

/**
 * Add running number. Override this:
 * - $this->runningNumberColumn: default number
 * - $this->runningNumberPrefix: default ''
 * - $this->runningNumberPrefix: 6
 */
trait HasRunningNumber
{
    public static function bootHasRunningNumber(): void
    {
        static::creating(function ($model) {
            $column = $model->rnColumn();

            if (empty($model->{$column})) {
                $model->{$column} = $model->generateRunningNumber();
            }
        });
    }

    public function rnColumn(): string
    {
        return $this->runningNumberColumn ?? 'number';
    }

    public function rnPrefix(): string
    {
        return $this->runningNumberPrefix ?? '';
    }

    public function rnPadding(): int
    {
        return $this->runningNumberPadding ?? 6;
    }

    protected function runningNumberScopeKey(): string
    {
        $tenantId = $this->tenant_id ?? 'global';

        return $tenantId.':'.static::class;
    }

    protected function generateRunningNumber(): string
    {
        $key = $this->runningNumberScopeKey();

        $next = DB::transaction(function () use ($key) {
            // avoid race on first insert for a brand new key
            DB::table('running_number_sequences')->insertOrIgnore([
                'key' => $key,
                'next_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $seq = DB::table('running_number_sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            DB::table('running_number_sequences')
                ->where('key', $key)
                ->update(['next_value' => $seq->next_value + 1]);

            return $seq->next_value;
        });

        return $this->rnPrefix()
            .str_pad((string) $next, $this->rnPadding(), '0', STR_PAD_LEFT);
    }
}
