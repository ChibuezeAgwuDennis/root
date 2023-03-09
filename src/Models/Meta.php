<?php

namespace Cone\Root\Models;

use Cone\Root\Casts\MetaValue;
use Cone\Root\Database\Factories\MetaFactory;
use Cone\Root\Interfaces\Models\Meta as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends Model implements Contract
{
    use InteractsWithProxy;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => MetaValue::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_meta_data';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MetaFactory::new();
    }

    /**
     * Get the metable model.
     */
    public function metable(): MorphTo
    {
        return $this->morphTo();
    }
}
