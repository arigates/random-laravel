<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityCreateRequest;
use App\Http\Requests\ActivityUpdateRequest;
use App\Models\Activity;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\throwException;

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
                $url = route('activities.edit', ['activity' => $data->id]);
                $btnEdit = '<a href="'.$url.'" class="btn btn-primary btn-sm btn-edit">Edit</a>';
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
        $rows = Product::all();
        $products = [];
        foreach ($rows as $row) {
            $products[] = [
                'id' => $row->id,
                'name' => $row->name,
                'min_price' => $row->min_price,
                'max_price' => $row->max_price,
            ];
        }

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
        DB::beginTransaction();
        try {
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
            $total = 0;
            foreach ($request->details as $detail) {
                $productId = $detail['product_id'];
                $price = $detail['price'];
                $product = Product::findOrFail($productId);
                if ($price < $product->min_price) {
                    DB::rollBack();

                    return response()->json(['message' => 'Harga lebih kecil dari batas harga bawah'], 400);
                }

                if ($price > $product->max_price) {
                    DB::rollBack();

                    return response()->json(['message' => 'Harga lebih kecil dari batas harga atas'], 400);
                }

                $products[$detail['product_id']] = [
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                ];

                $total += ($detail['qty'] * $detail['price']);
            }

            if ($total > $activity->budget) {
                DB::rollBack();

                return response()->json(['message' => 'Total melebihi budget'], 400);
            }

            $activity->products()->attach($products);

            DB::commit();

            return response()->json($activity, 201);
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json(['message' => $t->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Renderable
     */
    public function edit(int $id): Renderable
    {
        $activity = Activity::with('products')->findOrFail($id);
        $activity->budget = number_format($activity->budget,0,',','.');
        $rows = Product::all();
        $products = [];
        foreach ($rows as $row) {
            $products[] = [
                'id' => $row->id,
                'name' => $row->name,
                'min_price' => $row->min_price,
                'max_price' => $row->max_price,
            ];
        }
        $carts = [];
        foreach ($activity->products as $product) {
            $carts[] = [
                'product_id' => $product->id,
                'qty' => $product->pivot->qty,
                'price' => number_format($product->pivot->price,0,',','.'),
            ];
        }

        return view('activity.edit', compact('activity', 'products', 'carts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ActivityUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ActivityUpdateRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $activity = Activity::findOrFail($id);
            $activity->description = $request->description;
            $activity->date = $request->date;
            $activity->budget = $request->budget;
            if ($request->hasFile('document')) {
                $fileName = $request->file('document')->getClientOriginalName();
                $request->file('document')->storePubliclyAs('public/documents', $fileName);

                if ($activity->document) {
                    Storage::disk('public')->delete('/documents/'.$activity->document);
                }

                $activity->document = $fileName;
            }

            $activity->save();

            $activity->products()->detach();

            $products = [];
            $total = 0;
            foreach ($request->details as $detail) {
                $productId = $detail['product_id'];
                $price = $detail['price'];
                $product = Product::findOrFail($productId);
                if ($price < $product->min_price) {
                    DB::rollBack();

                    return response()->json(['message' => 'Harga lebih kecil dari batas harga bawah'], 400);
                }

                if ($price > $product->max_price) {
                    DB::rollBack();

                    return response()->json(['message' => 'Harga lebih kecil dari batas harga atas'], 400);
                }

                $products[$detail['product_id']] = [
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                ];

                $total += ($detail['qty'] * $detail['price']);
            }

            if ($total > $activity->budget) {
                DB::rollBack();

                return response()->json(['message' => 'Total melebihi budget'], 400);
            }

            $activity->products()->attach($products);

            DB::commit();

            return response()->json($activity, 201);
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json(['message' => $t->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $activity = Activity::findOrFail($id);
            $activity->products()->detach();
            if ($activity->document) {
                Storage::disk('public')->delete('/documents/'.$activity->document);
            }
            $activity->delete();
            DB::commit();

            return response()->json('Delete success');
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json(['message' => $t->getMessage()], 400);
        }
    }
}
