<?php

namespace App\Http\Controllers;


use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Validator;

class OfferController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_offers'])->only('index');
        $this->middleware(['permission:add_offer'])->only('create');
        $this->middleware(['permission:edit_offer'])->only('edit');
        $this->middleware(['permission:delete_offer'])->only('destroy');

        $this->offer_rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Image validation
            'status' => 'nullable|in:active,inactive', // Validating that status can only be 'active' or 'inactive'
        ];


        $this->offer_messages = [
            'title.required' => translate('Offer title is required'),
            'discount.required' => translate('Discount amount is required'),
            'discount.numeric' => translate('Discount must be a numeric value'),
            'start_date.required' => translate('Start date is required'),
            'end_date.required' => translate('End date is required'),
            'end_date.after_or_equal' => translate('End date must be after or equal to start date'),
            'status.required' => translate('Offer status is required'),
            'status.in' => translate('Invalid status. Choose Active or Inactive'),
        ];

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Initialize the offers query
        $offersQuery = Offer::select('id', 'title', 'description', 'discount', 'start_date', 'end_date', 'image','status')
            ->orderBy('created_at', 'desc');

        // Apply Search Filter on Offers
        if ($request->filled('search')) {
            $offersQuery->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('discount', 'like', '%' . $request->search . '%');
            });
        }

        // Paginate Offers
        $offers = $offersQuery->paginate(10)->appends([
            'search' => $request->search,
        ]);

        // Return the view with offers
        return view('backend.offers.index', compact('offers'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.offers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->offer_rules, $this->offer_messages);

        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $relativePath = null;
        if ($request->hasFile('image')) {
            $originalExtension = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . '.' . $originalExtension;
            $relativePath = 'offers_images/' . $fileName;
            $request->file('image')->move(public_path('offers_images'), $fileName);
        }

        Offer::create([
            'title' => $request->title,
            'description' => $request->description,
            'discount' => $request->discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image' => $relativePath,
        ]);

        flash(translate('Offer created successfully!'))->success();
        return redirect()->route('backend.offers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        // Eager load the customers related to the offer
        $offer->load('customers');

        // Return the view and pass the offer with its customers
        return view('backend.offers.customer-list', compact('offer'));
    }
// In OfferController.php

    public function removeCustomer(Request $request)
    {
        $offer = Offer::find($request->offer_id);
        $customer = User::find($request->customer_id);

        if ($offer && $customer) {
            // Remove customer from the offer
            $offer->customers()->detach($customer->id);
            return redirect()->route('backend.offers.show', $offer->id)->with('success', 'Customer successfully removed.');
        }

        return redirect()->route('backend.offers.show', $offer->id)->with('error', 'Error removing customer from offer.');
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        return view('backend.offers.edit', compact('offer'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), $this->offer_rules, $this->offer_messages);

        if ($validator->fails()) {
            // Flash error message and return with validation errors
            flash(translate('Sorry! Something went wrong'))->error();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the existing offer
        $offer = Offer::findOrFail($id);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // If a new image is uploaded, handle the image upload and delete the old one
            $originalExtension = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . '.' . $originalExtension;

            // Define the path where the image will be stored in the public folder
            $relativePath = 'offers_images/' . $fileName;

            // Move the uploaded file to the desired folder in the public directory
            $request->file('image')->move(public_path('offers_images'), $fileName);

            // Delete the old image if it exists
            if ($offer->image && file_exists(public_path('public/' . $offer->image))) {
                unlink(public_path('public/' . $offer->image));
            }

            // Update the image path in the database
            $offer->image = $relativePath;
        }

        // Update the offer record
        $offer->title = $request->title;
        $offer->description = $request->description;
        $offer->discount = $request->discount;
        $offer->start_date = $request->start_date;
        $offer->end_date = $request->end_date;
        $offer->status = $request->status; // Update the status

        // Save the offer
        $offer->save();

        // Flash success message and redirect
        flash(translate('Offer updated successfully!'))->success();
        return redirect()->route('backend.offers.index');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        try {
            // Optional: Detach customers before deleting the offer, if needed
            // $offer->customers()->detach();

            // Delete the offer
            $offer->delete();

            // Redirect to the offers list with a success message
            return redirect()->route('backend.offers.index')->with('success', 'Offer has been deleted successfully.');
        } catch (\Exception $e) {
            // Catch any errors and return an error message
            return redirect()->route('backend.offers.index')->with('error', 'An error occurred while deleting the offer.');
        }
    }



    public function showOffersAndCustomers(Request $request)
    {
        $offers = Offer::all();
        $customersQuery = User::where('user_type', 'customer');

        // Apply filters
        if ($request->filled('search')) {
            $customersQuery->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $customersQuery->where('status', $request->status);
        }

        // Exclude customers already attached to the selected offer
        if ($request->filled('offer_id')) {
            $offer = Offer::find($request->offer_id);
            if ($offer) {
                $attachedCustomerIds = $offer->customers()->pluck('users.id')->toArray();
                $customersQuery->whereNotIn('id', $attachedCustomerIds);
            }
        }

        $customers = $customersQuery->paginate(10)->appends($request->query());

        return view('backend.offers.select-customers', compact('offers', 'customers'));
    }

    public function attachCustomers(Request $request)
    {
        // Validate the request to ensure 'customer_ids' and 'offer_id' are present
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id', // Ensure that each ID exists in the users table
            'offer_id' => 'required|exists:offers,id', // Ensure offer_id exists in the offers table
        ]);

        // Retrieve the selected offer
        $offer = Offer::find($request->offer_id);

        if ($offer) {
            // Get the unique customer IDs from the request
            $customerIds = array_unique($request->customer_ids);

            // Retrieve the existing customer IDs that are already associated with the offer
            $existingCustomerIds = $offer->customers->pluck('id')->toArray();

            // Find the customer IDs that are not already attached to the offer
            $newCustomerIds = array_diff($customerIds, $existingCustomerIds);

            if (!empty($newCustomerIds)) {
                // Attach the new customers to the offer
                $offer->customers()->attach($newCustomerIds);

                return redirect()->back()->with('success', 'Customers successfully added to the offer.');
            } else {
                return redirect()->back()->with('info', 'No new customers to add. All selected customers are already attached to this offer.');
            }
        }

        return redirect()->route('offers.select-customers')->with('error', 'Offer not found.');
    }
    public function CustomerOfferIndex()
    {
        // Get the logged-in user ID
        $userId = auth()->user()->id;

        // Fetch active offers attached to the logged-in user using the pivot table
        $offers = Offer::where('status', 'active')
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter offers by user_id in the pivot table
            })
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        // Return the view with the offers data
        return view('frontend.Offer.index', compact('offers'));
    }

    public function CustomerOffershow($id)
    {
        $offer = Offer::findOrFail($id);
        return view('frontend.offer.show', compact('offer'));
    }


}
