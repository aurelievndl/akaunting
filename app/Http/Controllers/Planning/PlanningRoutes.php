<?php

namespace App\Http\Controllers\Planning;

use App\Abstracts\Http\Controller;
use App\Http\Requests\Planning\PlanningRoute as Request;
use App\Jobs\Setting\CreateTax;
use App\Jobs\Setting\DeleteTax;
use App\Jobs\Setting\UpdateTax;
use App\Models\Planning\PlanningRoute;

class PlanningRoutes extends Controller
{
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
        return view('planning.routes.create');
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
}
