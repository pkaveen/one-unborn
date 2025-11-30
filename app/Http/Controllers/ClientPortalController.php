<?php

namespace App\Http\Controllers;

use App\Models\ClientLink;
use App\Models\LinkMonitoringData;
use App\Models\SlaReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientPortalController extends Controller
{
    /**
     * Show the client login form
     */
    public function showLoginForm()
    {
        return view('client_portal.login');
    }

    /**
     * Handle client login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Handle client logout
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('client.login');
    }

    /**
     * Show client dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('client')->user();
        
        // Get all links for this client
        $links = ClientLink::where('client_id', $user->client_id)
            ->with(['router', 'deliverable'])
            ->active()
            ->get();
        
        // Get latest monitoring data for each link
        $linksWithStatus = $links->map(function ($link) {
            $latestData = LinkMonitoringData::where('client_link_id', $link->id)
                ->orderBy('collected_at', 'desc')
                ->first();
            
            $link->latest_data = $latestData;
            $link->is_up = $latestData && $latestData->interface_status === 'up';
            
            return $link;
        });
        
        // Get recent SLA reports
        $recentSlaReports = SlaReport::whereIn('client_link_id', $links->pluck('id'))
            ->orderBy('report_month', 'desc')
            ->take(5)
            ->with('clientLink')
            ->get();
        
        return view('client_portal.dashboard', [
            'links' => $linksWithStatus,
            'slaReports' => $recentSlaReports,
        ]);
    }

    /**
     * Show all client links
     */
    public function links()
    {
        $user = Auth::guard('client')->user();
        
        $links = ClientLink::where('client_id', $user->client_id)
            ->with(['router', 'deliverable', 'latestMonitoringData'])
            ->get();
        
        return view('client_portal.links', compact('links'));
    }

    /**
     * Show individual link details with real-time graphs
     */
    public function linkDetails($id)
    {
        $user = Auth::guard('client')->user();
        
        $link = ClientLink::where('client_id', $user->client_id)
            ->where('id', $id)
            ->with(['router', 'deliverable'])
            ->firstOrFail();
        
        // Get monitoring data for last 24 hours
        $monitoringData = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('collected_at', '>=', now()->subDay())
            ->orderBy('collected_at', 'asc')
            ->get();
        
        return view('client_portal.link_details', [
            'link' => $link,
            'monitoringData' => $monitoringData,
        ]);
    }

    /**
     * Show SLA reports
     */
    public function slaReports(Request $request)
    {
        $user = Auth::guard('client')->user();
        
        $query = SlaReport::whereHas('clientLink', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->with('clientLink.deliverable');
        
        // Filter by month if provided
        if ($request->filled('month')) {
            $query->where('report_month', $request->month);
        }
        
        // Filter by link if provided
        if ($request->filled('link_id')) {
            $query->where('client_link_id', $request->link_id);
        }
        
        $reports = $query->orderBy('report_month', 'desc')->paginate(15);
        
        // Get available links for filter dropdown
        $links = ClientLink::where('client_id', $user->client_id)
            ->with('deliverable')
            ->get();
        
        return view('client_portal.sla_reports', [
            'reports' => $reports,
            'links' => $links,
        ]);
    }

    /**
     * Download SLA report as PDF
     */
    public function downloadSlaReport($id)
    {
        $user = Auth::guard('client')->user();
        
        $report = SlaReport::whereHas('clientLink', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->with(['clientLink.router', 'clientLink.deliverable'])
          ->findOrFail($id);
        
        $pdf = \PDF::loadView('client_portal.pdf.sla_report', compact('report'));
        
        $filename = sprintf(
            'SLA_Report_%s_%s.pdf',
            $report->clientLink->deliverable->deliverable_id,
            $report->report_month
        );
        
        return $pdf->download($filename);
    }
}
