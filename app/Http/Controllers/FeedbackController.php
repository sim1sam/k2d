<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function feedbackForm()
    {
        return view('frontend.feedback');
    }

    public function storeFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'mobile_number' => 'required|max:20',
            'service_rating' => 'required|integer|min:1|max:5',
            'suggestion' => 'nullable|string',
            'birthday' => 'nullable|date_format:Y-m-d',
            'anniversary' => 'nullable|date_format:Y-m-d',
        ]);

        Feedback::create($request->all());

        flash('Feedback submitted successfully')->success();
        return redirect()->route('home');
    }

    public function adminFeedbacks()
    {
        // Fetch paginated feedback records in descending order
        $feedbacks = Feedback::orderBy('created_at', 'desc')->paginate(10); // Adjust the number as needed
        return view('backend.feedbacks.index', compact('feedbacks'));
    }
}