<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('product');
    }

    /**
     * @throws Exception
     */
    public function datatable(): JsonResponse
    {
        $products = Product::query();

        return DataTables::eloquent($products)
            ->addColumn('action', function ($data) {
                $btnEdit = '<a href="'.route('products.edit', ['product' => $data->id]).'" class="btn btn-primary btn-sm">Edit</a>';
                $btnDelete = '<button data-id="'.$data->id.'" class="btn btn-danger btn-sm ml-2 btn-delete">Hapus</button>';

                return $btnEdit.$btnDelete;
            })
            ->editColumn('min_price', function ($data) {
                return "Rp " . number_format($data->min_price,2,',','.');
            })
            ->editColumn('max_price', function ($data) {
                return "Rp " . number_format($data->max_price,2,',','.');
            })
            ->rawColumns(['action'])
            ->make();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCreateRequest $request
     * @return JsonResponse
     */
    public function store(ProductCreateRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json(ProductResource::make($product), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        return response()->json(ProductResource::make($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $update = $product->update($request->validated());
        if ($update) {
            return response()->json(ProductResource::make($product));
        } else {
            return response()->json('Update failed', 400);
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
        $product = Product::findOrFail($id);
        if ($product->delete()) {
            return response()->json('Delete success');
        } else {
            return response()->json('Delete failed', 400);
        }
    }
}
