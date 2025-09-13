<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:campaigns.list')->only('index');
        $this->middleware('permission:campaigns.view')->only('show');
        $this->middleware('permission:campaigns.create')->only(['create','store']);
        $this->middleware('permission:campaigns.update')->only(['edit','update']);
        $this->middleware('permission:campaigns.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $tab   = $request->get('tab', 'running'); // running|expired
        $query = Campaign::query();

        if ($tab === 'expired') $query->expired();
        else $query->running();

        if ($s = trim($request->get('q',''))) {
            $query->where(function($q) use ($s){
                $q->where('name','like',"%{$s}%")
                  ->orWhere('banner_text','like',"%{$s}%");
            });
        }

        $campaigns = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $runningCount = Campaign::running()->count();
        $expiredCount = Campaign::expired()->count();

        return view('campaigns.index', compact('campaigns','tab','runningCount','expiredCount','s'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(StoreCampaignRequest $request)
    {
        $data = $request->validated();

        if ($data['type']==='image' && $request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('campaigns','public');
        } else {
            $data['image_path'] = null;
            $data['image_size'] = null;
        }

        Campaign::create($data);

        return redirect()->route('campaigns.index')->with('status','Campaign created.');
    }

    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', compact('campaign'));
    }

    public function show(Campaign $campaign)
{
    return view('campaigns.show', compact('campaign'));
}


    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $data = $request->validated();

        // Handle image swap
        if ($data['type']==='image') {
            if ($request->hasFile('image')) {
                if ($campaign->image_path) Storage::disk('public')->delete($campaign->image_path);
                $data['image_path'] = $request->file('image')->store('campaigns','public');
            }
        } else {
            // switching to banner
            if ($campaign->image_path) Storage::disk('public')->delete($campaign->image_path);
            $data['image_path'] = null;
            $data['image_size'] = null;
        }

        $campaign->update($data);

        return redirect()->route('campaigns.index')->with('status','Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->image_path) Storage::disk('public')->delete($campaign->image_path);
        $campaign->delete();

        return back()->with('status','Campaign deleted.');
    }
}
