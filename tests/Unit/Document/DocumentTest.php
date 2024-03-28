<?php

use App\Http\Middleware\RoleMiddleware;
use App\Models\Document;
use App\Models\roles;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;


it('can upload a document', function () {


//    same test using pest test
login('uploader')->
post(route('create'), ['file' => UploadedFile::fake()->create('document.pdf', 100),])->
assertRedirect('home');

//check if document exists
$doc = Document::where('filename', 'document.pdf')->first();
expect($doc)->toBeInstanceOf(Document::class);

});
it('can download a document', function () {
//    create a document
$doc = Document::factory()->create();
//    download the document
$response = $this->post(route('download', $doc->id));
$response->assertStatus(200);
});
it('can update a document', function () {

//    create a document
$doc = Document::factory()->create();
//    update the document
$response = $this->patch(route('update', $doc->id), ['file' => UploadedFile::fake()->create('document.pdf', 100)]);
$response->assertRedirect('home');
});
it('can delete a document', function () {

//    create a document
$doc = Document::factory()->create();
//    delete the document
$response = $this->post(route('delete'), ['id' => $doc->id]);
$response->assertRedirect('home');

//check it does not exist in the database
$doc = Document::where('id', $doc->id)->first();
expect($doc)->toBeNull();
});

it('can approve a document', function () {

//    create a document
$doc = Document::factory()->create();

//    approve the document
login('reviewer')->post(route('approve'), ['id' => $doc->id])->assertRedirect('home');
//check if document is approved
$doc = Document::where('id', $doc->id)->first();
expect($doc->status)->toBe(Document::UNDER_FINALIZATION);

});
it('can reject a document', function () {

//    create a document
$doc = Document::factory()->create();
//    reject the document
login('reviewer')->post(route('reject'), ['popup-filename' => $doc->filename, 'reason' => 'reason'])->assertRedirect('home');
//check if document is rejected
$doc = Document::where('id', $doc->id)->first();
expect($doc->status)->toBe(Document::REJECTED);
});
//index
it('can view all documents', function () {
//    create a document
$doc = Document::factory()->create();
//    view all documents
login()->get(route('home'))->assertSee($doc->filename);
});

it('cannot approve the "Under_Review" document'  , function (string $role){

//    create document
    $doc = Document::factory()->create();
//    approve document with
    login($role)->post(route('approve'), ['id' => $doc->id]);
    $doc = Document::where('id', $doc->id)->first();
    expect($doc->status)->not->toBe(Document::UNDER_FINALIZATION);
})
//    call dataset and the ignore the role
->with(UserRoles('reviewer'));

it('cannot reject the "Under_Review" document'  , function (string $role){

//    create document
    $doc = Document::factory()->create();
//    reject document with
    login($role)->post(route('reject'), ['popup-filename' => $doc->filename, 'reason' => 'reason']);
    $doc = Document::where('id', $doc->id)->first();
    expect($doc->status)->not->toBe(Document::REJECTED);
})
//    call dataset and the ignore the role
->with(UserRoles('reviewer'));



it('cannot duplicate document in database', function () {

//    create a document
    $doc = Document::factory()->create();
//    upload a doc with the same name
    login('uploader')->
    post(route('create'), ['file' => UploadedFile::fake()->create($doc->filename, 100),])->
    assertRedirect('home')->assertSessionHas('error');

//    check in db there is only one file
    expect(count(Document::where('filename', $doc->filename)->get()))->toBeOne();
});

test('only uploader can upload', function ($role) {

//    upload a document ignore middleware
    login($role)->
    withoutMiddleware(RoleMiddleware::class)->
    post(route('create'), ['file' => UploadedFile::fake()->create('document.pdf', 100)])->
    assertRedirect('home')->assertSessionHas('error');
})->with(UserRoles('uploader'));

test('reason can\'t be null or more than 65534', function ($reason) {
//    create a document
    $doc = Document::factory()->create();
//    reject the document with the reason
    login('reviewer')->post(route('reject'), ['popup-filename' => $doc->filename, 'reason' => $reason])->
    assertRedirect('home')->assertSessionHas('error');

//    check if document is rejected
    $doc = Document::where('id', $doc->id)->first();
    expect($doc->status)->not->toBe(Document::REJECTED);
})->with(['', null, str_repeat('a', 65535)]);


it('can search for a document', function () {
//    create a document
$doc = Document::factory()->create();
//    search for the document
login()->get(route('search', ['query' => $doc->filename]))->assertSee($doc->filename);
});








