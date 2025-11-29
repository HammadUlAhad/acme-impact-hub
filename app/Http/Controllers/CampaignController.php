<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): Response
    {
        $campaigns = Campaign::with(['creator', 'donations'])
            ->when($request->search, function($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function($query, $category) {
                $query->where('cause_category', $category);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->where('status', '!=', Campaign::STATUS_DRAFT)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'category', 'status']),
            'categories' => Campaign::getCauseCategories(),
        ]);
    }

    public function create(): Response
    {
        // Temporarily disable authorization for testing
        // Gate::authorize('create', Campaign::class);

        return Inertia::render('Campaigns/Create', [
            'categories' => Campaign::getCauseCategories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Campaign::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'cause_category' => 'required|in:' . implode(',', array_keys(Campaign::getCauseCategories())),
            'goal_amount' => 'required|numeric|min:1|max:1000000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaigns', 'public');
        }

        Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'cause_category' => $request->cause_category,
            'goal_amount' => $request->goal_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image' => $imagePath,
            'created_by' => Auth::id(),
            'status' => Campaign::STATUS_PENDING,
        ]);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign created successfully and is pending approval.');
    }

    public function show(Campaign $campaign): Response
    {
        $campaign->load(['creator', 'approver', 'donations' => function($query) {
            $query->where('payment_status', Donation::STATUS_COMPLETED)
                  ->where('is_anonymous', false)
                  ->with('user')
                  ->latest()
                  ->limit(10);
        }]);

        $userDonation = null;
        if (Auth::check()) {
            $userDonation = $campaign->donations()
                ->where('user_id', Auth::id())
                ->where('payment_status', Donation::STATUS_COMPLETED)
                ->sum('amount');
        }

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'userDonation' => $userDonation,
            'canDonate' => $campaign->isAcceptingDonations(),
            'canEdit' => Auth::user()?->can('update', $campaign),
        ]);
    }

    public function edit(Campaign $campaign): Response
    {
        Gate::authorize('update', $campaign);

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'categories' => Campaign::getCauseCategories(),
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        Gate::authorize('update', $campaign);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'cause_category' => 'required|in:' . implode(',', array_keys(Campaign::getCauseCategories())),
            'target_amount' => 'required|numeric|min:1|max:1000000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title', 'description', 'cause_category', 'target_amount',
            'start_date', 'end_date'
        ]);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('campaigns', 'public');
        }

        // If campaign is approved and being edited, set back to pending
        if ($campaign->status === Campaign::STATUS_ACTIVE) {
            $data['status'] = Campaign::STATUS_PENDING;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        $campaign->update($data);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Approve a campaign (Admin/Campaign Manager only).
     */
    public function approve(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('approve', $campaign);

        $campaign->update([
            'status' => Campaign::STATUS_ACTIVE,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Campaign approved successfully.');
    }

    public function toggleFeature(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('feature', $campaign);

        $campaign->update([
            'is_featured' => !$campaign->is_featured,
        ]);

        $message = $campaign->is_featured ? 'Campaign featured successfully.' : 'Campaign unfeatured successfully.';

        return back()->with('success', $message);
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('delete', $campaign);

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
