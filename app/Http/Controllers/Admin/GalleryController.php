<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(
        private FileUploadService $fileUpload
    ) {}

    public function index()
    {
        $galleries = Gallery::ordered()->paginate(12);

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(StoreGalleryRequest $request)
    {
        $data = $request->validated();
        $data['order'] = $data['order'] ?? 0;
        $data['image'] = $this->fileUpload->upload($request->file('image'), 'galleries');

        Gallery::create($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Foto berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        $data = $request->validated();
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            $this->fileUpload->delete($gallery->image);
            $data['image'] = $this->fileUpload->upload($request->file('image'), 'galleries');
        }

        $gallery->update($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        $this->fileUpload->delete($gallery->image);
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('success', 'Foto berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        $galleries = Gallery::whereIn('id', $ids)->get();
        foreach ($galleries as $gallery) {
            $this->fileUpload->delete($gallery->image);
            $gallery->delete();
        }

        return redirect()->back()->with('success', count($ids).' foto berhasil dihapus.');
    }
}
