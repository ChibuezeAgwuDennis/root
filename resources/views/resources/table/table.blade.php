<div class="app-card" x-data="{ selection: [], all: false }">
    <div class="app-card__header">
        <h2 class="app-card__title">{{ $title }}</h2>
        @include('root::resources.table.filters')
    </div>
    <div class="app-card__body">
        <div class="data-table">
            @includeWhen(! empty($actions), 'root::resources.table.actions')
            @if($data->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table--hover">
                        <thead>
                            <tr>
                                @foreach($columns as $column)
                                    @include($column['template'], $column)
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                                <tr>
                                    @foreach($row['cells'] as $cell)
                                        @include($cell['template'], $cell)
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="data-table__footer">
                    <div class="data-table__footer-column">
                        <div class="form-group">
                            <label class="sr-only" for="per_page">
                                {{ __('Number of results') }}
                            </label>
                            <select
                                form="{{ $key }}"
                                class="form-control form-control--sm"
                                id="per_page"
                                name="per_page"
                            >
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected($option === $data->perPage())>
                                        {{ $option }}
                                    </option>
                                @endforeach
                                @if(! in_array($data->perPage(), $perPageOptions))
                                    <option value="{{ $data->perPage() }}" selected>
                                        {{ __('Custom (:perPage)', ['perPage' => $data->perPage()]) }}
                                    </option>
                                @endif
                            </select>
                        </div>
                        <p>{{ __('Showing :from to :to of :total results', ['from' => $data->firstItem(), 'to' => $data->lastItem(), 'total' => $data->total()]) }}</p>
                    </div>
                    {!! $data->links('root::resources.table.pagination') !!}
                </div>
            @else
                <x-root::alert>
                    {{ __('No resust found.') }}
                </x-root::alert>
            @endif
        </div>
    </div>
</div>