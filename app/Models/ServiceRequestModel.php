<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\GeoLocationService;
use Carbon;

class ServiceRequestModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "service_requests";
    protected $fillable = [
        'user_id',
        'property_id',
        'service_category_id',
        'service_id',
        'problem_title',
        'problem_description',
        'inspection_time',
        'inspection_date',
        'problem_images',
        'use_featured_providers',
        'featured_providers_id',
        'request_status',
        'department_id',
        'approved_providers_id',
        'approved_artisan_id',
        'total_cost',
        'old_featured_providers_id',
        'bidding_start_date',
        'bidding_end_date',
        'bidding_start_time',
        'bidding_end_time'
    ];

    protected $casts = [
        'problem_images' => 'array',
        'featured_providers_id' => 'array',
        'use_featured_providers' => 'boolean',
        'old_featured_providers_id' => 'array',
        'inspection_date' => 'date',
        'bidding_start_date' => 'date',
        'bidding_end_date' => 'date',
    ];
    
    
    protected $with = [
        'negotiations',
        'ratings',
        'user',
        'service_provider',
        'checkIns',
        'aportionment'
    ];

    public function negotiations()
    {
        return $this->hasMany(Negotiation::class, 'request_id');
    }

    public function submittedQuotes()
    {
        return $this->hasMany(SubmittedQuotes::class, 'request_id');
    }

    public function aportionment()
    {
        return $this->belongsTo(PaymentApportionment::class, 'service_request_id');
    }

    // Override the primary key type
    protected $keyType = 'string';

    // Disable auto-incrementing for the primary key
    public $incrementing = false;


    // Automatically generate a UUID when creating a new model instance
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Uuid::uuid4();
            }
        });
    }
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service_provider()
    {
        return $this->belongsTo(User::class, 'approved_providers_id');
    }

    public function artisan()
    {
        return $this->belongsTo(User::class, 'approved_artisan_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(Category::class, 'service_category_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reworkMessages()
    {
        return $this->hasMany(ReworkMessage::class, 'id');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class, 'service_request_id');
    }


    public function invited_artisan()
    {
        return $this->hasMany(ArtisanCanSubmitQuote::class, 'request_id');
    }

    // Handle featured providers (array)
    public function getFeaturedProvidersAttribute()
    {
        return User::whereIn('id', $this->featured_providers_id ?? [])->get();
    }

    public function ratings()
    {
        return $this->hasMany(ServiceRequestRatings::class, 'service_request_id', 'id');
    }
}
