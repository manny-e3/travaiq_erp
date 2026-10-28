<?php

namespace App\Http\Controllers;

use App\Models\KbArticle;
use Illuminate\Http\Request;

class KbController extends Controller
{
    /**
     * Display searchable index of knowledge base articles.
     */
    public function index(Request $request)
    {
        $query = KbArticle::published();

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $articles = $query->orderBy('views', 'desc')->get();

        // Group by category for structured visual display
        $groupedArticles = $articles->groupBy('category');

        return view('portal.kb.index', compact('groupedArticles', 'articles'));
    }

    /**
     * Display a specific KB article details and increment views.
     */
    public function show($slug)
    {
        $article = KbArticle::published()->where('slug', $slug)->firstOrFail();
        
        // Increment view count
        $article->increment('views');

        return view('portal.kb.show', compact('article'));
    }

    /**
     * AJAX search endpoint for real-time customer deflection and agent quick-insert.
     */
    public function ajaxSearch(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255'
        ]);

        $term = $request->q;

        if (empty($term)) {
            $results = KbArticle::published()->orderBy('views', 'desc')->limit(5)->get();
        } else {
            $results = KbArticle::published()
                ->search($term)
                ->orderBy('views', 'desc')
                ->get();
        }

        return response()->json($results);
    }
}
