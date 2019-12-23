<?php

namespace App\Http\Controllers\Modals;

use App\Abstracts\Http\Controller;
use App\Http\Requests\Planning\PlanningRoute as Request;
use App\Models\Planning\PlanningRoute;

class Planning extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $html = view('modals.planning_routes.create')->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
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
        $planning_route = PlanningRoute::create($request->all());

        $message = trans('messages.success.added');

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => $tax,
            'message' => $message,
            'html' => 'null',
        ]);
    }
}
