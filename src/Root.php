<?php

namespace Cone\Root;

use Closure;
use Cone\Root\Breadcrumbs\Registry as Breadcrumbs;
use Cone\Root\Navigation\Registry as Navigation;
use Cone\Root\Resources\Resources;
use Cone\Root\Widgets\Widgets;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Root
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '2.0.0-beta';

    /**
     * The registered booting callbacks.
     */
    protected array $booting = [];

    /**
     * The Application instance.
     */
    public readonly Application $app;

    /**
     * The resources collection.
     */
    public readonly Resources $resources;

    /**
     * The widgets collection.
     */
    public readonly Widgets $widgets;

    /**
     * The navigation instance.
     */
    public readonly Navigation $navigation;

    /**
     * The breadcrumbs instance.
     */
    public readonly Breadcrumbs $breadcrumbs;

    /**
     * Create a new Root instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->resources = new Resources();
        $this->widgets = new Widgets();
        $this->navigation = new Navigation();
        $this->breadcrumbs = new Breadcrumbs();
    }

    /**
     * Resolve the Root instance from the container.
     */
    public static function instance(): static
    {
        return App::make(static::class);
    }

    /**
     * Boot the Root application.
     */
    public function boot(): void
    {
        $this->resources->each->boot($this);

        foreach ($this->booting as $callback) {
            call_user_func_array($callback, [$this]);
        }

        $this->breadcrumbs->patterns([
            $this->getPath() => __('Dashboard'),
            sprintf('%s/{resource}', $this->getPath()) => static function (Request $request): string {
                return $request->route('_resource')->getName();
            },
            sprintf('%s/{resource}/create', $this->getPath()) => __('Create'),
            sprintf('%s/{resource}/{resourceModel}', $this->getPath()) => static function (Request $request): string {
                return $request->route('_resource')->modelTitle($request->route('resourceModel'));
            },
            sprintf('%s/{resource}/{resourceModel}/edit', $this->getPath()) => __('Edit'),
        ]);
    }

    /**
     * Register a booting callback.
     */
    public function booting(Closure $callback): void
    {
        $this->booting[] = $callback;
    }

    /**
     * Determine if Root should run on the given request.
     */
    public function shouldRun(Request $request): bool
    {
        $host = empty($this->getDomain())
            ? parse_url(Config::get('app.url'), PHP_URL_HOST)
            : $this->getDomain();

        $segments = explode('/', $request->getRequestUri());

        return (empty($this->getDomain()) || $request->getHost() === $host)
            && ($this->getPath() === '/' || $segments[1] === trim($this->getPath(), '/'));
    }

    /**
     * Register the root routes.
     */
    public function routes(Closure $callback): void
    {
        Route::as('root.')
            ->domain($this->getDomain())
            ->prefix($this->getPath())
            ->middleware(['root'])
            ->group($callback);
    }

    /**
     * Get the Root URI path.
     */
    public function getPath(): string
    {
        return Str::start(Config::get('root.path', 'root'), '/');
    }

    /**
     * Get the Root domain.
     */
    public function getDomain(): string
    {
        return (string) Config::get('root.domain', null);
    }
}
