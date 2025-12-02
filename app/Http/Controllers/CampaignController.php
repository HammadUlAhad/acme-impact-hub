<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category', 'status']);
        $campaigns = $this->campaignService->getCampaigns($filters, 12);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $filters,
            'categories' => Campaign::getCauseCategories(),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Campaign::class);

        return Inertia::render('Campaigns/Create', [
            'categories' => Campaign::getCauseCategories(),
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = $this->campaignService->createCampaign($data, Auth::user());

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

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('featured_image')) {
            // Delete old image if it exists
            if ($campaign->featured_image) {
                Storage::disk('public')->delete($campaign->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('campaigns', 'public');
        }

        $this->campaignService->updateCampaign($campaign, $data);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function approve(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('approve', $campaign);

        $this->campaignService->approveCampaign($campaign, Auth::user());

        return back()->with('success', 'Campaign approved successfully.');
    }

    public function toggleFeature(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('feature', $campaign);

        $campaign = $this->campaignService->toggleFeatured($campaign);
        $message = $campaign->is_featured 
            ? 'Campaign featured successfully.' 
            : 'Campaign unfeatured successfully.';

        return back()->with('success', $message);
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('delete', $campaign);

        // Delete associated image
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $this->campaignService->deleteCampaign($campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
