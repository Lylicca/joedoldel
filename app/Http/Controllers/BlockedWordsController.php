<?php

namespace App\Http\Controllers;

use App\Models\BlockedWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class BlockedWordsController extends Controller
{
    private function invalidateCache()
    {
        Cache::forget('blocked_words');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('blockedWords/index', [
            'words' => Inertia::defer(fn() => BlockedWord::all()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        BlockedWord::create([
            'word' => strtolower($request->input('word')),
            'weight' => (int) $request->input('weight') / 100,
        ]);

        $this->invalidateCache();

        return redirect()->route('blocked-words.index')->with('success', 'Blocked word added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        $blockedWord = BlockedWord::findOrFail($id);
        $blockedWord->update([
            'word' => strtolower($request->input('word')),
            'weight' => (int) $request->input('weight') / 100,
        ]);

        $this->invalidateCache();

        return redirect()->route('blocked-words.index')->with('success', 'Blocked word updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blockedWord = BlockedWord::findOrFail($id);
        $blockedWord->delete();

        $this->invalidateCache();

        return redirect()->route('blocked-words.index')->with('success', 'Blocked word deleted successfully.');
    }
}
