<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model): Response
    {
        $field = $request->route('field');

        // Gate::allowIf($field->authorized($request, $model));

        return ResponseFactory::view(
            'root::resources.relation',
            $field->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Inertia\Response
     */
    public function create(CreateRequest $request, Model $model): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Form',
            $field->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = tap($relation->getRelated(), static function (Model $related) use ($model): void {
            $related->setRelation('parent', $model);
        });

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $relation->save($related);

        $path = sprintf('%s/%s', $request->resolved()->resolveUri($request), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Inertia\Response
     */
    public function show(Request $request, Model $model, Model $related): Response
    {
        return Inertia::render(
            'Relations/Show',
            $request->resolved()->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Inertia\Response
     */
    public function edit(Request $request, Model $model, Model $related): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Form',
            $field->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->resolved();

        $related->setRelation('parent', $model);

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $related->save();

        $path = sprintf('%s/%s/edit', $request->resolved()->resolveUri($request), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        $trashed = class_uses_recursive(SoftDeletes::class) && $related->trashed();

        $trashed ? $related->forceDelete() : $related->delete();

        $path = $request->resolved()->resolveUri($request);

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
