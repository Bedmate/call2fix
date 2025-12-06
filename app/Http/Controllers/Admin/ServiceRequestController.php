<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessOfficeAddress;
use App\Models\Category;
use App\Models\Property;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $query = ServiceRequest::query();
        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('problem_title', 'like', "%{$search}%")
                    ->orWhere('problem_description', 'like', "%{$search}%");
            });
        }

        if (request()->has('status')) {
            $status = request()->input('status');
            $query->where('request_status', $status);
        }

        $serviceRequests = $query->latest()->paginate(get_settings_value('per_page') ?? 10);
        return view('admin.service-requests.index', compact('serviceRequests'));
    }

    public function create()
    {
        $users = User::role(["co-operate_accounts", "private_accounts"])
            ->with('properties')
            ->get();

        $categories = Category::all();
        $services   = Service::all();

        return view('admin.service-requests.create', compact('users', 'categories', 'services'));
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id'               => 'required|exists:users,id',
            'property_id'           => 'required|exists:properties,id',
            'service_category_id'   => 'nullable|exists:categories,id',
            'service_id'            => 'nullable|exists:services,id',
            'problem_title'         => 'required|string|max:255',
            'problem_description'   => 'required|string',
            'inspection_time'       => 'required',
            'inspection_date'       => 'required|date',
            'problem_images'        => 'nullable|array|max:5',
            'use_featured_providers' => 'boolean',
            'featured_providers_id' => 'nullable|array',
            'bidding_start_date'    => 'nullable|date',
            'bidding_end_date'      => 'nullable|date',
            'bidding_start_time'    => 'nullable',
            'bidding_end_time'      => 'nullable'
        ]);

        if ($validate->fails()) {
            return back()->withErrors($validate->errors())->withInput();
        }

        $data = $validate->validated();
        $data['request_status'] = "Pending";

        /** ---------------------------------------------------------
         *  FIND NEAREST PROVIDERS (same as customer flow)
         *  ---------------------------------------------------------
         */
        $alphameadAccount = get_settings_value(
            'alphamaed_service_account_id',
            'a599fd50-15b4-4db5-a839-9e722aea226d'
        );

        // If admin manually selects featured providers, skip auto-fetch
        if (empty($request->featured_providers_id)) {

            $property = Property::findOrFail($request->property_id);
            $radiusLimitMeters = 20000; // 20KM radius or use $this->radiusLimitKm if applicable

            $latitude = $property->porperty_latitude;
            $longitude = $property->porperty_longitude;

            $selectedCategoryId = $request->service_category_id;

            $providers = BusinessOfficeAddress::query()
                ->select(
                    'business_office_addresses.user_id',
                    DB::raw("
                    ST_Distance_Sphere(
                        point(business_office_addresses.longitude, business_office_addresses.latitude),
                        point(?, ?)
                    ) as distance
                ")
                )
                ->join('business_infos', 'business_infos.user_id', '=', 'business_office_addresses.user_id')
                ->whereRaw("JSON_CONTAINS(business_infos.businessCategory, ?)", [json_encode($selectedCategoryId)])
                ->orderBy('distance')
                ->groupBy('business_office_addresses.user_id')
                ->addBinding([$longitude, $latitude], 'select')
                ->get()
                ->pluck('user_id')
                ->toArray();

            // Filter only users with 'providers' role
            $distinctProviders = User::whereIn('id', $providers)
                ->whereHas('roles', fn($q) => $q->where('name', 'providers'))
                ->pluck('id')
                ->toArray();

            // Randomly pick the FIRST 5 providers
            $distinctProviders = collect($distinctProviders)->shuffle()->take(5)->toArray();

            // Ensure Alphamead is added
            if (!in_array($alphameadAccount, $distinctProviders)) {
                $distinctProviders[] = $alphameadAccount;
            }

            // If still empty, abort with error
            if (empty($distinctProviders)) {
                return back()->with('error', 'No service provider found nearby.')->withInput();
            }

            $data['featured_providers_id'] = $distinctProviders;
        }

        /** ---------------------------------------------------------
         *  SAVE REQUEST 
         *  ---------------------------------------------------------
         */
        $serviceRequest = ServiceRequest::create($data);

        return redirect()->route('admin.service-requests.index')
            ->with('success', 'Service request created successfully.');
    }



    public function show(ServiceRequest $serviceRequest)
    {
        return view('admin.service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        return view('admin.service-requests.edit', compact('serviceRequest'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validate = Validator::make($request->all(), [
            'property_id'            => 'required|exists:properties,id',
            'service_category_id'    => 'nullable|exists:categories,id',
            'service_id'             => 'nullable|exists:services,id',
            'problem_title'          => 'required|string|max:255',
            'problem_description'    => 'required|string',
            'inspection_time'        => 'required|date_format:H:i',
            'inspection_date'        => 'required|date',
            'problem_images'         => 'nullable|array',
            'use_featured_providers' => 'boolean',
            'featured_providers_id'  => 'nullable|array',
        ]);

        if ($validate->fails()) {
            return get_error_response("Validation Error", $validate->errors()->toArray());
        }

        $serviceRequest->update($validate->validated());

        return redirect()->route('admin.service-requests.index')->with('success', 'Service request updated successfully.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();
        return redirect()->route('admin.service-requests.index')->with('success', 'Service request deleted successfully.');
    }

    public function createOnBehalfOfCustomer()
    {
        $customers = User::role('customer')->get();
        return view('admin.service-requests.create-on-behalf', compact('customers'));
    }

    public function storeOnBehalfOfCustomer(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'customer_id'            => 'required|exists:customers,id',
            'property_id'            => 'required|exists:properties,id',
            'service_category_id'    => 'nullable|exists:categories,id',
            'service_id'             => 'nullable|exists:services,id',
            'problem_title'          => 'required|string|max:255',
            'problem_description'    => 'required|string',
            'inspection_time'        => 'required|date_format:H:i',
            'inspection_date'        => 'required|date',
            'problem_images'         => 'nullable|array',
            'use_featured_providers' => 'boolean',
            'featured_providers_id'  => 'nullable|array',
        ]);

        if ($validate->fails()) {
            return get_error_response("Validation Error", $validate->errors()->toArray());
        }

        $serviceRequest = ServiceRequest::create($validate->validated());

        return redirect()->route('admin.service-requests.index')->with('success', 'Service request created on behalf of customer successfully.');
    }
}
