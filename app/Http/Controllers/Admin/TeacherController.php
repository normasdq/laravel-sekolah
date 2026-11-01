<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        private FileUploadService $fileUpload
    ) {}

    public function index(Request $request)
    {
        $query = Teacher::ordered();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $teachers = $query->paginate(10)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->fileUpload->upload($request->file('photo'), 'teachers');
        }

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                $this->fileUpload->delete($teacher->photo);
            }
            $data['photo'] = $this->fileUpload->upload($request->file('photo'), 'teachers');
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            $this->fileUpload->delete($teacher->photo);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        $teachers = Teacher::whereIn('id', $ids)->get();
        foreach ($teachers as $teacher) {
            if ($teacher->photo) {
                $this->fileUpload->delete($teacher->photo);
            }
            $teacher->delete();
        }

        return redirect()->back()->with('success', count($ids).' data berhasil dihapus.');
    }
}
