<?php

namespace App\Http\Controllers\Planning;

use App\Traits\HereMapsApi;
use App\Abstracts\Http\Controller;
use App\Models\Planning\PlanningRoute;
use App\Jobs\Planning\CreatePlanningRoute;
use App\Http\Requests\Planning\PlanningRoute as Request;

class PlanningRoutes extends Controller
{
    use HereMapsApi;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-planning-routes')->only(['create', 'store']);
        $this->middleware('permission:read-planning-routes')->only(['index', 'edit']);
        $this->middleware('permission:update-planning-routes')->only(['update', 'enable', 'disable']);
        $this->middleware('permission:delete-planning-routes')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $planning_routes = PlanningRoute::collect();

        return view('planning.routes.index', compact('planning_routes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sequence = $this->waypoints()->toJson();
        $route = static::drawRoute($this->waypoints('waypoint'))->toJson();

        return view('planning.routes.create', compact('sequence', 'route'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        return [];
    }

    /**
     * Example points
     */
    private function waypoints($param_key = null)
    {
        return collect([
            "51.0543,3.7174",
            "50.8195,3.2577",
            "50.8798,4.7005",
            "51.0543,3.7174",
        ])->mapWithKeys(function($value, $key) use ($param_key) {
            return [
                $param_key.$key => $value,
            ];
        });
    }
}
