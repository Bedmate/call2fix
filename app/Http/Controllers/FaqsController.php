<?php

namespace App\Http\Controllers;

use App\Mail\SupportEmail;
use App\Models\Faqs as Faq;
use Illuminate\Http\Request;
use Mail;
use Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FaqsController extends Controller
{
    public function __construct()
    {
        // if(!Schema::hasColumn('faqs', 'account_type')) {
        //     Schema::table('faqs', function(Blueprint $table) {
        //         $table->string('account_type')->nullable();
        //     });
        // }
    }

    public function index()
    {
        $faqs = Faq::where('account_type', active_role)->get();
        return get_success_response($faqs);
    }

    public function show(Faq $faq)
    {
        return get_success_response($faq);
    }

    public function sendSupportEmail(Request $request)
    {
        $supportEmail = get_settings_value('support_email');

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'account_type' => 'required',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return get_error_response("Validation error", $validator->errors()->toArray(), 422);
        }

        try {
            $emailData = $validator->validated();
            $emailData['sender'] = auth()->user();

            $send = Mail::to($supportEmail)->send(new SupportEmail($emailData));
            if ($send) {
                return get_success_response(['message' => 'Email sent successfully'], 'Email sent successfully');
            }

            return get_error_response('Failed to send email');
        } catch (\Exception $e) {
            return get_error_response('Failed to send email: ' . $e->getMessage(), ['error' => $e->getMessage()]);
        }
    }

    // Show all FAQs
    public function all()
    {
        $faqs = Faq::latest()->paginate(10);
        return view('faqs.index', compact('faqs'));
    }

    // Show create form
    public function create()
    {
        $roles = Role::with('permissions')->get();
        return view('faqs.create');
    }

    // Store new FAQ
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'user_role' => 'required|string|max:255',
            '_account_type' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
        ]);

        Faqs::create($data);

        return redirect()->route('faqs.index')->with('success', 'FAQ created successfully.');
    }

    // Show single FAQ
    public function view($id)
    {
        $faq = Faq::findOrFail($id);
        return view('faqs.show', compact('faq'));
    }

    // Show edit form
    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('faqs.edit', compact('faq'));
    }

    // Update FAQ
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $data = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'user_role' => 'sometimes|string|max:255',
            '_account_type' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
        ]);

        $faq->update($data);

        return redirect()->route('faqs.index')->with('success', 'FAQ updated successfully.');
    }

    // Soft delete FAQ
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->route('faqs.index')->with('success', 'FAQ deleted successfully.');
    }

    // Restore soft-deleted FAQ
    public function restore($id)
    {
        $faq = Faq::withTrashed()->findOrFail($id);
        $faq->restore();

        return redirect()->route('faqs.index')->with('success', 'FAQ restored successfully.');
    }

    // Permanently delete soft-deleted FAQ
    public function forceDelete($id)
    {
        $faq = Faq::withTrashed()->findOrFail($id);
        $faq->forceDelete();

        return redirect()->route('faqs.index')->with('success', 'FAQ permanently deleted.');
    }
}
