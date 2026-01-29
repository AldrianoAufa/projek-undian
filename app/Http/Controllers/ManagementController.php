<?php

namespace App\Http\Controllers;

use App\Models\Management;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
        ]);

        $position = Position::findOrFail($request->position_id);
        $data = $request->only(['nama_lengkap', 'position_id']);
        $data['jabatan'] = $position->name;

        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('management/photos', 'public');
        }

        if ($request->filled('ttd_drawing')) {
            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'management/signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($fileName, base64_decode($imageData));
            $data['ttd'] = $fileName;
        } elseif ($request->hasFile('ttd')) {
            $data['ttd'] = $request->file('ttd')->store('management/signatures', 'public');
        }

        Management::create($data);

        return redirect()->back()->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $management = Management::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
        ]);

        $position = Position::findOrFail($request->position_id);
        $data = $request->only(['nama_lengkap', 'position_id']);
        $data['jabatan'] = $position->name;

        if ($request->hasFile('foto_profil')) {
            if ($management->foto_profil) {
                Storage::disk('public')->delete($management->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('management/photos', 'public');
        }

        if ($request->filled('ttd_drawing')) {
            if ($management->ttd) {
                Storage::disk('public')->delete($management->ttd);
            }
            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'management/signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($fileName, base64_decode($imageData));
            $data['ttd'] = $fileName;
        } elseif ($request->hasFile('ttd')) {
            if ($management->ttd) {
                Storage::disk('public')->delete($management->ttd);
            }
            $data['ttd'] = $request->file('ttd')->store('management/signatures', 'public');
        }

        $management->update($data);

        return redirect()->back()->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $management = Management::findOrFail($id);

        if ($management->foto_profil) {
            Storage::disk('public')->delete($management->foto_profil);
        }
        if ($management->ttd) {
            Storage::disk('public')->delete($management->ttd);
        }

        $management->delete();

        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus.');
    }
}
