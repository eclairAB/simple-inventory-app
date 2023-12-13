<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function __invoke()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%");
                        // ->orWhere('email', 'LIKE', "%{$value}%");
                });
            });
        });
        $products = QueryBuilder::for(Product::class)
        ->defaultSort('name')
        ->allowedSorts(['id', 'name', 'price' ,'stock'])
        ->allowedFilters(['id', 'name', 'price' ,'stock', $globalSearch])
        ->paginate(8)
        ->withQueryString();

        return Inertia::render('Product', ['products' => $products])->table(function (InertiaTable $table) {
            $table->column('id', 'ID', searchable: true, sortable: true);
            $table->column('name', 'Product Name', searchable: true, sortable: true);
            $table->column('price', 'Price', searchable: true, sortable: true);
            $table->column('stock', 'Stock', searchable: true, sortable: true);
        });
    }
}
