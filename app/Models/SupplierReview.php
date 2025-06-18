<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'reviewer_id',
        'supplier_id',
        'product_accuracy',
        'timeliness',
        'condition_on_arrival',
        'communication',
        'professionalism',
        'value_for_money',
        'comment',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
