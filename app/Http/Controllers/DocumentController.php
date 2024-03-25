<?php

namespace App\Http\Controllers;

use App\Mail\DocumentAction;
use App\Models\Document;
use App\Models\User;
use App\Models\user_roles;
use Illuminate\Support\Facades\Mail;

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
    public function download( $document)
    {

    $doc= Document::find($document);
//    download file contents from database
    $fileContent = base64_decode($doc->content);

    $filename = $doc->filename;
//    get file db to storage path
    file_put_contents(storage_path('app/UploadedDocs/' . $filename), $fileContent);
    $file= storage_path('app/UploadedDocs/' . $filename);
//download then delete file
    return response()->download($file)->deleteFileAfterSend(true);

    }
    public function update( $document)
    {
        return view('update',
            [
                'document' => Document::find($document),
            ]
        );
    }
    public function delete()
    {
        $document = request('id');
        $doc= Document::find($document);
        $doc->delete();
        return redirect()->route('home');
    }
    public function storeInDB($filename)
    {
//        check if document exists
        $doc= \App\Models\Document::where('filename', $filename)->first();
        if($doc){
            $message = 'Document already exists';
            return redirect()->route('home')->with('error', $message);
        }
        $document = new Document();
        $document->filename = $filename;
//        Unable to encode attribute [content] for model [App\Models\Document] to JSON: The content must be a string or null, you passed a 'array'
//        so we need to encode the content to base64
        $document->content = base64_encode(file_get_contents(storage_path('app/UploadedDocs/' . $filename)));
        $document->save();
        //        delete all files from storage
        $files = glob(storage_path('app/UploadedDocs/*'));
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
        return redirect()->route('home');
    }
    public function create()
    {
//        check permission
        if(auth()->user()->getRole()->role_slug !== 'uploader'){
            $message = 'You do not have permission to upload documents';
            return redirect()->route('home')->with('error', $message);
        }
        $file= request()->file('file');
        $file->storeAs('UploadedDocs', $file->getClientOriginalName());
        return $this->storeInDB($file->getClientOriginalName());

    }

    public function approve()
    {
        $document = request('id');
        $doc= Document::find($document);

//        check permission
        if((auth()->user()->getRole()->role_slug == 'reviewer' && $doc->status == Document::UNDER_REVISION) || (auth()->user()->getRole()->role_slug == 'finalizer' && $doc->status == Document::UNDER_FINALIZATION)){
            $doc->approve();
            $doc->save();
            //        mail to the responsible party depending on the document status
            $this->BuildSendMail($doc, 'approved');

        }else{
            $message = 'You do not have permission to approve documents';
            return redirect()->route('home')->with('error', $message);
        }


        return redirect()->route('home');
    }
    public function reject()
    {
        $filename = request('popup-filename');

        $doc= Document::where('filename', $filename)->first();

//check reason size
        if(strlen(request('reason')) > 65534){
            $message = 'Reason can\'t be more than 65534 characters';

            return redirect()->route('home')->with('error', $message);
        }



//        Mail::to("yousefhussen139@gmail.com")->send(new DocumentAction('rejected', route('download', $doc->id), request('reason')));

//        check permission
        if((auth()->user()->getRole()->role_slug == 'reviewer' && $doc->status == 'Under_Revision') || (auth()->user()->getRole()->role_slug == 'finalizer' && $doc->status == 'Under_Finalization')){
            $doc->reject(request('reason'));
            $doc->save();

            //        mail to the responsible party depending on the document status
            $this->BuildSendMail($doc, 'rejected');

        }else{
            $message = 'You do not have permission to reject documents';
            return redirect()->route('home')->with('error', $message);
        }
        return redirect()->route('home');
    }

    public function BuildSendMail($doc , $action){

        switch ($doc->status){
            case Document::UNDER_REVISION:
                Mail::to(User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'reviewer');
                })->first()->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
                break;
            case Document::UNDER_FINALIZATION:
                Mail::to(User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'finalizer');
                })->first()->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
                break;

            case Document::APPROVED || Document::REJECTED:
                Mail::to(User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'uploader');
                })->first()->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
        }
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
