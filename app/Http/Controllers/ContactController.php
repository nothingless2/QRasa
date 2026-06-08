<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = Contact::all();
        return view('contact.index', compact('contact'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contact.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ig'    => 'required|string|max:255',
            'wa'    => 'required|string|max:255',
            'fb'    => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        $contact        = new contact();
        $contact->ig    = $request->ig;
        $contact->wa    = $request->wa;
        $contact->email = $request->email;
        $contact->fb    = $request->fb;

        $contact->save();
        return redirect()->route('contact.index')->with('success', 'Kontak berhasil ditambahkan!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('contact.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'ig'    => 'required|string|max:255',
            'wa'    => 'required|string|max:255',
            'fb'    => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        $contact        = new Contact();
        $contact->ig    = $request->ig;
        $contact->wa    = $request->wa;
        $contact->email = $request->email;
        $contact->fb    = $request->fb;

        $contact->save();
        return redirect()->route('contact.index')->with('success', 'Kontak berhasil ditambahkan!');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contact.index')->with('success', 'Kontak berhasil dihapus!');
    }
}
