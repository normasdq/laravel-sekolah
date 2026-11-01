<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->filled('filter')) {
            if ($request->input('filter') === 'unread') {
                $query->unread();
            } elseif ($request->input('filter') === 'read') {
                $query->read();
            }
        }

        $contacts = $query->paginate(15)->withQueryString();
        $unreadCount = Contact::unread()->count();

        return view('admin.contacts.index', compact('contacts', 'unreadCount'));
    }

    public function show(Contact $contact)
    {
        if (! $contact->is_read) {
            $contact->markAsRead();
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Pesan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        Contact::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids).' pesan berhasil dihapus.');
    }

    public function markAllRead()
    {
        Contact::unread()->update(['is_read' => true]);

        return redirect()->route('admin.contacts.index')->with('success', 'Semua pesan ditandai sudah dibaca.');
    }
}
