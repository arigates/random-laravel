<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @method static static create(array $attributes = [])
 * @method static static findOrFail($id, $columns = ['*'])
 *
 * @property-read int $id
 * @property string $name
 * @property float $min_price
 * @property float $max_price
 */

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_price',
        'max_price',
    ];

    protected $casts = [
        'min_price' => 'int',
        'max_price' => 'int',
    ];
}
