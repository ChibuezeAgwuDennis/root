<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\NotificationFactory;
use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Support\Filters;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\URL;

class Notification extends DatabaseNotification implements Contract
{
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_created_at',
        'is_read',
        'url',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_notifications';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the formatted created at attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function formattedCreatedAt(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                return Filters::apply(
                    'root:notification.formatted_created_at_attribute',
                    $this->created_at?->isoFormat('YYYY. MMMM DD. HH:mm'),
                    $this
                );
            }
        );
    }

    /**
     * Get the is read at attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isRead(): Attribute
    {
        return new Attribute(
            get: function (): bool {
                return Filters::apply(
                    'root:notification.is_read_attribute',
                    ! is_null($this->read_at),
                    $this
                );
            }
        );
    }

    /**
     * Get the URL attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function url(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                return Filters::apply(
                    'root:notification.url_attribute',
                    $this->exists ? URL::route('root.api.notifications.update', $this) : null,
                    $this
                );
            }
        );
    }
}
