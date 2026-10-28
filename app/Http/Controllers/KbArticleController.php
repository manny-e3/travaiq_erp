<?php

namespace App\Http\Controllers;

use App\Models\KbArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KbArticleController extends Controller
{
    /**
     * Display listing of all articles.
     */
    public function index()
    {
        $articles = KbArticle::orderBy('created_at', 'desc')->get();
        return view('admin.helpdesk.kb.index', compact('articles'));
    }

    /**
     * Show form to create an article.
     */
    public function create()
    {
        return view('admin.helpdesk.kb.create');
    }

    /**
     * Store newly created article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:Technical,Billing,Account,Feedback,General',
            'tags_input' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);

        // Parse comma-separated tags into array
        $tags = [];
        if (!empty($request->tags_input)) {
            $tags = array_map('trim', explode(',', $request->tags_input));
            $tags = array_filter($tags); // Remove empty values
        }

        KbArticle::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'tags' => $tags,
            'is_published' => $validated['is_published'],
        ]);

        return redirect()->route('admin.kb-articles.index')
            ->with('success', 'Knowledge Base Article created successfully.');
    }

    /**
     * Show form to edit an article.
     */
    public function edit($id)
    {
        $kbArticle = KbArticle::findOrFail($id);
        return view('admin.helpdesk.kb.edit', compact('kbArticle'));
    }

    /**
     * Update an article.
     */
    public function update(Request $request, $id)
    {
        $kbArticle = KbArticle::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:Technical,Billing,Account,Feedback,General',
            'tags_input' => 'nullable|string',
            'is_published' => 'required|boolean',
            'slug' => 'nullable|string|max:255',
        ]);

        $tags = [];
        if (!empty($request->tags_input)) {
            $tags = array_map('trim', explode(',', $request->tags_input));
            $tags = array_filter($tags);
        }

        $slug = empty($request->slug) ? Str::slug($validated['title']) : Str::slug($request->slug);

        $kbArticle->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'tags' => $tags,
            'is_published' => $validated['is_published'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.kb-articles.index')
            ->with('success', 'Knowledge Base Article updated successfully.');
    }

    /**
     * Delete an article.
     */
    public function destroy($id)
    {
        $kbArticle = KbArticle::findOrFail($id);
        $kbArticle->delete();

        return redirect()->route('admin.kb-articles.index')
            ->with('success', 'Knowledge Base Article deleted successfully.');
    }
}
