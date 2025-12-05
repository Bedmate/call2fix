<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
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
            'inspection_time'       => 'required|date_format:H:i',
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
