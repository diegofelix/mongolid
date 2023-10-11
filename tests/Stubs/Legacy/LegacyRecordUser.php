<?php

namespace Mongolid\Tests\Stubs\Legacy;

use Mongolid\LegacyRecord;

class LegacyRecordUser extends LegacyRecord
{
    public bool $mutable = true;

    public bool $dynamic = false;

    protected string $collection = 'users';

    protected bool $timestamps = true;

    /**
     * @var string[]
     */
    protected array $fillable = [
        'name',
    ];

    public function siblings()
    {
        return $this->embedsMany(LegacyRecordUser::class, 'siblings');
    }

    public function grandsons()
    {
        return $this->referencesMany(LegacyRecordUser::class, 'grandsons');
    }

    public function setSecretAttribute($value): string
    {
        return 'password_override';
    }
}
