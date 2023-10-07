<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Builder
 * @method static static create(array $attributes = [])
 * @method static static findOrFail($id, $columns = ['*'])
 *
 * @property-read int $id
 * @property string $description
 * @property string $date
 * @property float $budget
 * @property string $document
 */
class Activity extends Model
{
    protected $fillable = [
        'description',
        'date',
        'budget',
        'document',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'activity_products', 'activity_id', 'product_id')->withPivot([
            'qty',
            'price',
        ]);
    }
}
