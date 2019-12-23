@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.items', 1)]))

@section('content')
    <div class="card">
        {!! Form::open([
            'route' => 'planning.routes.store',
            'id' => 'item',
            '@submit.prevent' => 'onSubmit',
            '@keydown' => 'form.errors.clear($event.target.name)',
            'files' => true,
            'role' => 'form',
            'class' => 'form-loading-button',
            'novalidate' => true
        ]) !!}

            <div class="card-body">
                <div class="row">
                    {{ Form::textGroup('name', trans('general.name'), 'tag') }}
                </div>

                <leaflet-map
                    :sequence="{{ $sequence }}"
                    :draw-route="{{ $route }}"
                    :here-api-key="{{ config('here.api_key') }}"
                    :here-app-id="{{ config('here.app_id') }}"
                    :here-app-code="{{ config('here.app_code') }}"
                ></leaflet-map>
            </div>

            <div class="card-footer">
                <div class="row float-right">
                    {{ Form::saveButtons('planning/routes') }}
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@push('scripts_start')
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.polyline.snakeanim@0.2.0/L.Polyline.SnakeAnim.min.js"></script>
@endpush
