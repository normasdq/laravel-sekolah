<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SawCriterion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SawCriterionController extends Controller
{
    public function index()
    {
        $criteria = SawCriterion::orderBy('sort_order')->orderBy('id')->get();
        $totalActiveWeight = (float) $criteria->where('is_active', true)->sum('weight');

        return view('admin.spk.criteria.index', compact('criteria', 'totalActiveWeight'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:saw_criteria,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:benefit,cost'],
            'weight' => ['required', 'numeric', 'gt:0', 'max:1000'],
            'source' => ['required', 'in:manual,cbt_score'],
            'min_value' => ['required', 'numeric', 'min:0'],
            'max_value' => ['required', 'numeric', 'gt:min_value'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Kode kriteria wajib diisi (contoh: C5).',
            'code.unique' => 'Kode kriteria tersebut sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'weight.gt' => 'Bobot kriteria harus lebih besar dari 0.',
            'max_value.gt' => 'Nilai maksimum harus lebih besar dari nilai minimum.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['sort_order'] = $validated['sort_order'] ?? (SawCriterion::max('sort_order') + 1);
        $validated['is_active'] = $request->boolean('is_active', true);

        SawCriterion::create($validated);

        return redirect()
            ->route('admin.spk.criteria.index')
            ->with('success', 'Kriteria SAW baru berhasil ditambahkan.');
    }

    public function update(Request $request, SawCriterion $criterion)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('saw_criteria', 'code')->ignore($criterion->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:benefit,cost'],
            'weight' => ['required', 'numeric', 'gt:0', 'max:1000'],
            'source' => ['required', 'in:manual,cbt_score'],
            'min_value' => ['required', 'numeric', 'min:0'],
            'max_value' => ['required', 'numeric', 'gt:min_value'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.unique' => 'Kode kriteria tersebut sudah digunakan.',
            'weight.gt' => 'Bobot kriteria harus lebih besar dari 0.',
            'max_value.gt' => 'Nilai maksimum harus lebih besar dari nilai minimum.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['sort_order'] = $validated['sort_order'] ?? $criterion->sort_order;
        $validated['is_active'] = $request->boolean('is_active', false);

        $criterion->update($validated);

        return redirect()
            ->route('admin.spk.criteria.index')
            ->with('success', 'Kriteria '.$criterion->code.' berhasil diperbarui.');
    }

    public function destroy(SawCriterion $criterion)
    {
        $code = $criterion->code;
        $criterion->delete();

        return redirect()
            ->route('admin.spk.criteria.index')
            ->with('success', 'Kriteria '.$code.' berhasil dihapus.');
    }
}

