<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    // Display dashboard with search functionality
    public function index(Request $request)
    {
        $query = Journal::where('user_id', Auth::id());

        // Process search if query parameter exists
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $allJournals = Journal::where('user_id', Auth::id())->get();
        $filteredJournals = $query->latest()->get();

        return view('layouts/dashboard', [
            'journals' => $allJournals,
            'totalJournals' => $allJournals->count(),
            'filteredJournals' => $filteredJournals,
            'isLoading' => false
        ]);
    }

    // Show create page
    public function create()
    {
        return view('journals/create');
    }

    // Store new journal
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Journal::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Journal created!');
    }

    // Update journal
    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('dashboard')->with('success', 'Journal updated!');
    }

    // Delete journal (Soft Delete)
    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->delete(); // Moves to trash instead of permanently deleting

        return redirect()->route('dashboard')->with('success', 'Journal moved to trash!');
    }

    // --- TRASH FUNCTIONALITY ---

    // Load recently deleted items
    public function trash()
    {
        $trashedJournals = Journal::onlyTrashed()
            ->where('user_id', Auth::id())
            ->latest('deleted_at')
            ->get();

        return view('layouts/recently-deleted', [
            'trashedJournals' => $trashedJournals,
            'hasDeletedItems' => $trashedJournals->isNotEmpty()
        ]);
    }

    // Restore an item from the trash
    public function restore($id)
    {
        $journal = Journal::onlyTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->restore();

        return redirect()->route('recently-deleted')->with('success', 'Journal restored!');
    }

    // Permanently delete a single item
    public function forceDelete($id)
    {
        $journal = Journal::onlyTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->forceDelete();

        return redirect()->route('recently-deleted')->with('success', 'Journal permanently deleted!');
    }

    // Permanently delete everything in the trash
    public function emptyTrash()
    {
        Journal::onlyTrashed()->where('user_id', Auth::id())->forceDelete();

        return redirect()->route('recently-deleted')->with('success', 'Trash Emptied!');
    }

    // Security check
    private function authorizeJournal($journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
