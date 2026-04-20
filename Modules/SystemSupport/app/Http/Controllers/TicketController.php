<?php

namespace Modules\SystemSupport\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SystemSupport\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected function isAdminUser(): bool
    {
        return Auth::user()?->hasRole('Admin') === true;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Ticket::with('user');
        
        if (!$this->isAdminUser()) {
            $query->where('user_id', Auth::id());
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total' => Ticket::when(!$this->isAdminUser(), fn($q) => $q->where('user_id', Auth::id()))->count(),
            'open' => Ticket::when(!$this->isAdminUser(), fn($q) => $q->where('user_id', Auth::id()))->where('status', 'Open')->count(),
            'in_progress' => Ticket::when(!$this->isAdminUser(), fn($q) => $q->where('user_id', Auth::id()))->where('status', 'In Progress')->count(),
            'resolved' => Ticket::when(!$this->isAdminUser(), fn($q) => $q->where('user_id', Auth::id()))->whereIn('status', ['Resolved', 'Closed'])->count(),
        ];

        return view('systemsupport::tickets.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        return view('systemsupport::tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'module' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'description' => 'required|string'
        ]);

        Ticket::create([
            'title' => $request->title,
            'module' => $request->module,
            'priority' => $request->priority,
            'description' => $request->description,
            'status' => 'Open',
            'user_id' => Auth::id()
        ]);

        return redirect()->route('systemsupport.tickets.index')->with('success', 'Ticket berhasil dikirim! Tim IT akan segera menindaklanjutinya.');
    }

    public function show($id)
    {
        $ticket = Ticket::with('user')->findOrFail($id);
        $currentUserId = (int) Auth::id();
        $ticketOwnerId = (int) $ticket->user_id;
        
        if (!$this->isAdminUser() && $ticketOwnerId !== $currentUserId) {
            abort(403);
        }

        return view('systemsupport::tickets.show', compact('ticket'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        if (!$this->isAdminUser()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'admin_response' => 'nullable|string'
        ]);

        $ticket->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response
        ]);

        return redirect()->back()->with('success', 'Status/Respon ticket berhasil di-update.');
    }
}
