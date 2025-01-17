<?php

namespace App\Http\Controllers;

use App\Models\Document;



class DocumentController extends Controller
{
    public function index()
    {
        return view('home',
            [
                'documents' => Document::all(),
            ]
        );
    }

    public function download($document)
    {

        $doc = Document::find($document);
        $path = storage_path('app/UploadedDocs/' . $doc->filename);
        file_put_contents($path, $doc->getContent());
        return response()->download($path)->deleteFileAfterSend(true);

    }

    public function update($document)
    {
        $doc = Document::find($document);
        $file = request()->file('file');
        $file->storeAs('UploadedDocs', $file->getClientOriginalName());
        $doc->filename = $file->getClientOriginalName();
        $doc->content = base64_encode(file_get_contents(storage_path('app/UploadedDocs/' . $file->getClientOriginalName())));
        $doc->save();
        //        delete all files from storage
        clearStorage();
        return redirect()->route('home');
    }

    public function delete()
    {
        $document = request('id');
        $doc = Document::find($document);
        $doc->delete();
        return redirect()->route('home');
    }

    public function create()
    {
//        check permission
        if (auth()->user()->getRole()->role_slug !== 'uploader') {
            $message = 'You do not have permission to upload documents';
            return redirect()->route('home')->with('error', $message);
        }
        $file = request()->file('file');
        $file->storeAs('UploadedDocs', $file->getClientOriginalName());
        return $this->storeInDB($file->getClientOriginalName());

    }

    public function storeInDB($filename)
    {
//        check if document exists
        $doc = Document::where('filename', $filename)->first();
        if ($doc) {
            $message = 'Document already exists';
            return redirect()->route('home')->with('error', $message);
        }
        $document = new Document();
        $document->filename = $filename;
        $document->SetContent(file_get_contents(storage_path('app/UploadedDocs/' . $filename)));
        $document->save();
        clearStorage();
        return redirect()->route('home');
    }

    public function approve()
    {
        $document = request('id');
        $doc = Document::find($document);

//        check permission
        if ((auth()->user()->getRole()->role_slug == 'reviewer' && $doc->status == Document::UNDER_REVISION) || (auth()->user()->getRole()->role_slug == 'finalizer' && $doc->status == Document::UNDER_FINALIZATION)) {
            $doc->approve();
            $doc->save();
            //        mail to the responsible party depending on the document status
            EmailController::BuildSendMail($doc, 'approved');
        } else {
            $message = 'You do not have permission to approve documents';
            return redirect()->route('home')->with('error', $message);
        }


        return redirect()->route('home');
    }

    public function reject()
    {
        $filename = request('popup-filename');

        $doc = Document::where('filename', $filename)->first();

//check reason size is less than 65534 and greater than 0
        if (strlen(request('reason')) > 65534 || strlen(request('reason')) < 1) {
            $message = 'Reason can\'t be more than 65534 characters';

            return redirect()->route('home')->with('error', $message);
        }


//        Mail::to("yousefhussen139@gmail.com")->send(new DocumentAction('rejected', route('download', $doc->id), request('reason')));

//        check permission
        if ((auth()->user()->getRole()->role_slug == 'reviewer' && $doc->status == 'Under_Revision') || (auth()->user()->getRole()->role_slug == 'finalizer' && $doc->status == 'Under_Finalization')) {
            $doc->reject(request('reason'));
            $doc->save();

            //        mail to the responsible party depending on the document status
            EmailController::BuildSendMail($doc, 'rejected');

        } else {
            $message = 'You do not have permission to reject documents';
            return redirect()->route('home')->with('error', $message);
        }
        return redirect()->route('home');
    }


    public function search()
    {
        $search = request('query');
//        sanitize search
        $search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $documents = Document::where('filename', 'like', '%' . $search . '%')->get();
        return view('home',
            [
                'documents' => $documents,
            ]
        );
    }
}
