<?php

namespace Mongolid\Tests\Stubs\Legacy;

use Mongolid\LegacyRecord;
use Mongolid\Tests\Stubs\Price;

class Product extends LegacyRecord
{
    public $with = [
        'price' => [
            'key' => '_id',
            'model' => Price::class,
        ],
        'shop' => [
            'key' => 'skus.shop_id',
            'model' => Shop::class,
        ],
    ];

    protected string $collection = 'products';

    public function price()
    {
        return $this->referencesOne(Price::class, '_id');
    }

    public function skus()
    {
        return $this->embedsMany(Sku::class, 'skus');
    }
}
