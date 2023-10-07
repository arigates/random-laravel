<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityCreateRequest;
use App\Models\Activity;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('activity.index');
    }

    /**
     * @throws Exception
     */
    public function datatable(): JsonResponse
    {
        $activities = Activity::query();

        return DataTables::eloquent($activities)
            ->addColumn('action', function ($data) {
                $btnEdit = '<button data-id="'.$data->id.'" class="btn btn-primary btn-sm btn-edit">Edit</button>';
                $btnDelete = '<button data-id="'.$data->id.'" class="btn btn-danger btn-sm ml-2 btn-delete">Hapus</button>';

                return $btnEdit.$btnDelete;
            })
            ->editColumn('budget', function ($data) {
                return "Rp " . number_format($data->budget,2,',','.');
            })
            ->editColumn('document', function ($data) {
                $url = Storage::disk('public')->url('documents/'.$data->document);
                return '<a href="'.$url.'">'.$data->document.'</a>';
            })
            ->rawColumns(['action', 'document'])
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create(): Renderable
    {
        $products = Product::all();

        return view('activity.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ActivityCreateRequest $request
     * @return JsonResponse
     */
    public function store(ActivityCreateRequest $request): JsonResponse
    {
        $activity = new Activity();
        $activity->description = $request->description;
        $activity->date = $request->date;
        $activity->budget = $request->budget;
        if ($request->hasFile('document')) {
            $fileName = $request->file('document')->getClientOriginalName();
            $request->file('document')->storePubliclyAs('public/documents', $fileName);

            $activity->document = $fileName;
        }
        $activity->save();

        $products = [];
        foreach ($request->details as $detail) {
            $products[$detail['product_id']] = [
                'qty' => $detail['qty'],
                'price' => $detail['price'],
            ];
        }

        $activity->products()->attach($products);

        return response()->json($activity);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
