<?php

namespace App\Http\Controllers;

use App\Models\GoogleDocs;
use Illuminate\Http\Request;

class GoogleDocsController extends Controller
{
    public function index()
    {
        return GoogleDocs::paginate(5);
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'text' => ''
        ]);

        $sheet = GoogleDocs::create($request->all());
        
        $docs = new GoogleDocs();
        $sheetUrl = $docs->createSheet($sheet['id'], [
            $request['firstName'],
            $request['lastName'],
            $request['phone'],
            $request['text']
        ]);

        $sheet->update(['sheetUrl' => $sheetUrl]);

        return response('', 200);
    }
}
