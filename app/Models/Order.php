<?php
namespace App\Models;

use App\Services\GeoLocationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Order extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'seller_id',
        'status',
        'quantity',
        'total_price',
        'estimated_delivery',
        'is_leasable',
        'order_id',
        'kwik_order_id',
        'duration_type',
        'lease_duration',
        'lease_rate',
        'lease_notes',
        'delivery_address',
        'delivery_longitude',
        'delivery_latitude',
        'additional_info',
        'shipping_fee',
        'product_category_id',
        'delivery_type',
        'order_accepted_time'
    ];

    protected $hidden = [
        'kwik_order_id',
    ];

    public function leasableProducts()
    {
        return $this->where('is_leasable', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Product::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public const STATUSES = [
        0  => 'UPCOMING',
        1  => 'STARTED',
        2  => 'ENDED',
        3  => 'FAILED',
        4  => 'ARRIVED',
        6  => 'UNASSIGNED',
        7  => 'ACCEPTED',
        8  => 'DECLINE',
        9  => 'CANCEL',
        10 => 'DELETED',
        11 => 'REJECTED',
    ];

    // Mutator to set status
    public function setStatusAttribute($value)
    {
        // Check if the passed value is an integer (array key)
        if (is_numeric($value) && array_key_exists((int) $value, self::STATUSES)) {
            $this->attributes['status'] = self::STATUSES[(int) $value]; // Convert to string status
        }
        // If the value is a string, map it to the corresponding integer key and then to a string
        elseif (is_string($value)) {
            $statusKey = array_search(strtoupper($value), self::STATUSES);
            if ($statusKey !== false) {
                $this->attributes['status'] = self::STATUSES[$statusKey]; // Store as string
            } else {
                $this->attributes['status'] = $value; // Fallback in case value is unknown
            }
        } else {
            $this->attributes['status'] = $value; // Store whatever is passed (fallback)
        }
    }

    // Accessor to get status description
    public function getStatusDescriptionAttribute()
    {
        return self::STATUSES[$this->attributes['status']] ?? 'Unknown';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->order_id) {
                $model->order_id      = (string) (self::count() + 1);
                $model->_account_type = active_role();
            }
        });
    }

    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted);
        if (request()->has('category') && ! empty(request()->query('category'))) {
            $query->where('product_category_id', request()->query('category'));
        }

        if (request()->has('status') && ! empty(request()->query('status'))) {
            $query->where('status', request()->query('status'))
                ->orWhere('order_status', request()->query('status'));
        }

        return $query;
    }

    // Accessor for created_at
    public function getCreatedAtAttribute($value)
    {
        return $this->convertToCustomerTimezone($value);
    }

    // Accessor for updated_at
    public function getUpdatedAtAttribute($value)
    {
        return $this->convertToCustomerTimezone($value);
    }

    // Accessor for deleted_at
    public function getDeletedAtAttribute($value)
    {
        return $this->convertToCustomerTimezone($value);
    }

    // Helper method to handle conversion
    protected function convertToCustomerTimezone($value)
    {
        if (blank($value)) {
            return null;
        }

        // Ensure it's a Carbon instance
        $carbon = $value instanceof Carbon ? $value : Carbon::parse($value);

        // Resolve GeoLocationService directly via Laravel's service container
        $timezone = app(GeoLocationService::class)->getUserTimezoneByIp();

        // Convert to user's timezone
        return $carbon->shiftTimezone($timezone);
    }
}
