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
        $query = Journal::where('user_id', Auth::id());

        $allUserJournals = Journal::where('user_id', Auth::id())->latest()->get();
        $availableMonths = $allUserJournals->groupBy(function($journal) {
            return $journal->created_at->format('F Y');
        })->keys();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('month') && $request->month != '') {
            try {
                $date = Carbon::createFromFormat('F Y', $request->month);
                $query->whereMonth('created_at', $date->month)
                      ->whereYear('created_at', $date->year);
            } catch (\Exception $e) {
                // Ignore
            }
        }

        if ($request->has('sort') && $request->sort == 'oldest') {
            $filteredJournals = $query->oldest()->get();
        } else {
            $filteredJournals = $query->latest()->get();
        }

        $groupedJournals = $filteredJournals->groupBy(function($journal) {
            return $journal->created_at->format('F Y');
        });

        if ($request->ajax()) {
            return view('partials.journal-list', [
                'journals' => $allUserJournals,
                'groupedJournals' => $groupedJournals,
                'isLoading' => false
            ])->render();
        }

        return view('layouts.dashboard', [
            'journals' => $allUserJournals,
            'totalJournals' => $allUserJournals->count(),
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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:50',
        ]);

        Journal::create([
            'title' => $request->title,
            'content' => $request->content,
            'mood' => $request->mood,
            'is_favorite' => $request->has('is_favorite'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Journal created!');
    }

    public function show($id)
    {
        $journal = Journal::withTrashed()->findOrFail($id);
        $this->authorizeJournal($journal);

        return view('journals/show', compact('journal'));
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
            'mood' => $request->mood,
            'is_favorite' => $request->has('is_favorite'),
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

    // TRASH FUNCTIONALITY

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

        $trashedJournals = $query->latest('deleted_at')->get();

        $groupedJournals = $trashedJournals->groupBy(function($journal) {
            return $journal->deleted_at->format('F Y');
        });

        return view('layouts/recently-deleted', [
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
