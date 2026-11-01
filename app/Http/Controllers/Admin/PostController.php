<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(
        private FileUploadService $fileUpload
    ) {}

    public function index(Request $request)
    {
        $query = Post::with(['category', 'user'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        // Slug Logic
        $data['slug'] = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);
        $data['user_id'] = auth()->id();
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUpload->upload($request->file('image'), 'posts');
        }

        $data['content'] = $this->sanitizeHtml($data['content']);

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();

        $data['slug'] = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->fileUpload->delete($post->image);
            }
            $data['image'] = $this->fileUpload->upload($request->file('image'), 'posts');
        }

        $data['content'] = $this->sanitizeHtml($data['content']);

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui.');
    }

    private function sanitizeHtml(string $content): string
    {
        $allowedTags = '<p><br><strong><em><b><i><u><s><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><table><thead><tbody><tr><th><td><a><img><hr>';
        $cleaned = strip_tags($content, $allowedTags);
        $cleaned = preg_replace('/(\s+)(on\w+|formaction)\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $cleaned);
        $cleaned = preg_replace('/href\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', 'href="#"', $cleaned);

        return $cleaned;
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            $this->fileUpload->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        $posts = Post::whereIn('id', $ids)->get();
        foreach ($posts as $post) {
            if ($post->image) {
                $this->fileUpload->delete($post->image);
            }
            $post->delete();
        }

        return redirect()->back()->with('success', count($ids).' berita berhasil dihapus.');
    }
}
