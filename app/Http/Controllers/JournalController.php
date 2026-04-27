<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Efficiently get available months for the filter dropdown
        $availableMonths = Journal::where('user_id', $userId)
            ->selectRaw("DISTINCT strftime('%F %Y', created_at) as month_year")
            ->orderBy('created_at', 'desc')
            ->pluck('month_year')
            ->map(function($monthYear) {
                // strftime '%F %Y' gives YYYY-MM-DD YYYY which is wrong for SQLite
                // Actually SQLite strftime('%m %Y', created_at) would be better
                return $monthYear;
            });

        // Correction for SQLite month formatting:
        $availableMonths = Journal::where('user_id', $userId)
            ->selectRaw("DISTINCT strftime('%m %Y', created_at) as m_y")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($row) {
                return Carbon::createFromFormat('m Y', $row->m_y)->format('F Y');
            })->unique()->values();

        $query = Journal::where('user_id', $userId);

        // 2. Apply Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // 3. Apply Month Filter
        if ($request->filled('month')) {
            try {
                $date = Carbon::createFromFormat('F Y', $request->month);
                $query->whereMonth('created_at', $date->month)
                      ->whereYear('created_at', $date->year);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        // 4. Apply Sorting
        if ($request->input('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        // 5. Paginate results
        $journals = $query->paginate(15)->withQueryString();

        // Group the PAGINATED results for the view
        $groupedJournals = $journals->groupBy(function($journal) {
            return $journal->created_at->format('F Y');
        });

        if ($request->ajax()) {
            return view('components/journal-list', [
                'journals' => $journals,
                'groupedJournals' => $groupedJournals,
                'isLoading' => false
            ])->render();
        }

        return view('dashboard', [
            'journals' => $journals,
            'totalJournals' => Journal::where('user_id', $userId)->count(),
            'groupedJournals' => $groupedJournals,
            'availableMonths' => $availableMonths,
            'isLoading' => false
        ]);
    }

    public function create()
    {
        return view('journals/create');
    }

    public function store(Request $request)
    {
        // 1. Added validation for the new fields
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:50',
        ]);

        Journal::create([
            'title' => $request->title,
            'content' => $request->content,
            'mood' => $request->mood, // Save mood
            'is_favorite' => $request->has('is_favorite'), // Checkboxes return true if checked
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Journal created!');
    }

    public function show($id)
    {
        $journal = Journal::withTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        return view('journals/view-entry', compact('journal'));
    }

    public function edit($id)
    {
        $journal = Journal::findOrFail($id);
        $this->authorizeJournal($journal);

        return view('journals/edit', compact('journal'));
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);
        $this->authorizeJournal($journal);

        // 2. Added validation for updates
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:50',
        ]);

        $journal->update([
            'title' => $request->title,
            'content' => $request->content,
            'mood' => $request->mood, // Update mood
            'is_favorite' => $request->has('is_favorite'), // Update favorite status
        ]);

        return redirect()->route('dashboard')->with('success', 'Journal updated!');
    }

    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->delete();

        return redirect()->route('dashboard')->with('success', 'Journal moved to trash!');
    }

    // --- TRASH FUNCTIONALITY ---

    public function trash(Request $request)
    {
        $query = Journal::onlyTrashed()->where('user_id', Auth::id());

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $trashedJournals = $query->latest('deleted_at')->paginate(15);

        $groupedJournals = $trashedJournals->groupBy(function($journal) {
            return $journal->deleted_at->format('F Y');
        });

        return view('journals.trash', [
            'journals' => $trashedJournals,
            'groupedJournals' => $groupedJournals,
            'hasDeletedItems' => $trashedJournals->isNotEmpty()
        ]);
    }

    public function restore($id)
    {
        $journal = Journal::onlyTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->restore();

        return redirect()->route('recently-deleted')->with('success', 'Journal restored!');
    }

    public function forceDelete($id)
    {
        $journal = Journal::onlyTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        $journal->forceDelete();

        return redirect()->route('recently-deleted')->with('success', 'The journal entry was permanently deleted.');
    }

    public function emptyTrash()
    {
        Journal::onlyTrashed()->where('user_id', Auth::id())->forceDelete();

        return redirect()->route('recently-deleted')->with('success', 'All trash has been permanently deleted.');
    }

    private function authorizeJournal($journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
