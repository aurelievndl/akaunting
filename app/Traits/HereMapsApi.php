<?php

namespace App\Traits;

use Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait HereMapsApi
{
    public static $base_uri = "https://api.testing.com/";

    public static $cache_minutes = 20;
    public static $find_sequence_uri = "https://wse.ls.hereapi.com/2/findsequence.json";
    public static $calculate_route_uri = "https://route.ls.hereapi.com/routing/7.2/calculateroute.json";

    /**
     * Send request to Here API
     */
    protected static function hereMapsRequest($method, $path, $extra_data = [])
    {
        $client = new Client(['verify' => false, 'base_uri' => static::$base_uri]);

        $headers = [];
        $data = [
            'timeout' => 30,
            'referer' => true,
            'http_errors' => false,
            'query' => array_merge([
                'apiKey' => config('here.api_key'),
                'app_id' => config('here.app_id'),
                'app_code' => config('here.app_code'),
            ], $extra_data['query']),
        ];

        $options = array_merge($data, $headers);

        try {
            $response = $client->request($method, $path, $options);
        } catch (RequestException $e) {
            $response = $e;
        }

        return $response;
    }

    /**
     * Calculate route between given points
     */
    public static function drawRoute($waypoints = [])
    {
        return $calculateroute = Cache::remember('calculateroute', static::$cache_minutes, function () use ($waypoints) {

            $parameters = $waypoints;
            $parameters->put('mode', "fastest;car;traffic:enabled");
            $parameters->put('representation', 'display');
            $parameters->put('alternatives', 4);

            if (!$response = static::getResponseData("GET", static::$calculate_route_uri, ['query' => $parameters->toArray()])) {
                throw new \Exception(trans('modules.errors.download', ['module' => 'planning_routes']));
            }

            return collect($response->route[0]->shape);
        });
    }

    /**
     * Calculate best sequence between given points
     */
    public static function calculateRouteSequence($waypoints = [])
    {
        return Cache::remember('find_sequence', static::$cache_minutes, function () use ($waypoints) {

            $parameters = $waypoints->mapWithKeys(function($value, $key) use ($parameters) {
                $param_key = "destination" . $key;

                if ($key == 0) $param_key = "start";
                if ($key == ($parameters->count() - 1)) $param_key = "end";

                return [
                    $param_key => $value,
                ];
            });
            $parameters->put('mode', "fastest;car;traffic:enabled");
            $parameters->put('departure', "now");

            if (!$response = static::getResponseData("GET", static::$find_sequence_uri, ['query' => $parameters->toArray()])) {
                throw new \Exception(trans('modules.errors.download', ['module' => 'planning_routes']));
            }

            return collect($response->results[0]->waypoints);
        });
    }

    /**
     * Get Response
     */
    public static function getResponse($method, $path, $data = [])
    {
        $response = static::hereMapsRequest($method, $path, $data);

        if (!$response || ($response instanceof RequestException) || ($response->getStatusCode() != 200)) {
            return false;
        }

        return $response;
    }

    /**
     * Get Response Data
     */
    public static function getResponseData($method, $path, $data = [])
    {
        if (!$response = static::getResponse($method, $path, $data)) {
            return [];
        }

        $body = json_decode($response->getBody());

        if (!is_object($body)) {
            return [];
        }

        return $body->response;
    }
}
