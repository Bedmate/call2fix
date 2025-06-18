<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SupplierReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $request->merge(['reviewer_id' => Auth::id()]);
            $validated = $request->validate([
                'product_accuracy'     => 'required|integer|between:1,5',
                'timeliness'           => 'required|integer|between:1,5',
                'condition_on_arrival' => 'required|integer|between:1,5',
                'communication'        => 'required|integer|between:1,5',
                'professionalism'      => 'required|integer|between:1,5',
                'value_for_money'      => 'required|integer|between:1,5',
                'comment'              => 'nullable|string',
            ]);

            $review = SupplierReview::create([
                'product_id'  => $product->id,
                'supplier_id' => $product->seller_id, 
                'reviewer_id' => Auth::id(),
                ...$validated,
            ]);

            return get_success_response($review, 'Review submitted successfully.', 200);
        } catch (\Exception $e) {
            return get_error_response($e->getMessage(), []);
        }
    }

    public function index(Request $request)
    {
        $reviews = SupplierReview::with(['product', 'reviewer'])->latest()->paginate(10);
        return get_success_response($reviews);
    }
}
