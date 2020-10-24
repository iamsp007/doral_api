<?php

namespace App\Http\Controllers;

use App\Models\emailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return emailTemplate::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Post data
        $request = json_decode($request->getContent(), true);
        $email = $request['data'];
        $data = array(
            'title' => $email['title'],
            'subject' => $email['subject'],
            'body' => $email['body'],
            'is_attached' => $email['is_attached'],
            'status' => 'Inactive'
        );
        $id = emailTemplate::insert($data);
        if ($id) {
            dd($email);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\emailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(emailTemplate $emailTemplate)
    {
        return emailTemplate::find($emailTemplate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\emailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(emailTemplate $emailTemplate)
    {
        $EmailTemplate = emailTemplate::findOrFail($emailTemplate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\emailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, emailTemplate $emailTemplate)
    {
        $EmailTemplate = emailTemplate::findOrFail($emailTemplate);
        $EmailTemplate->update($request->all());

        return response()->json($EmailTemplate, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\emailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(emailTemplate $emailTemplate)
    {
        $EmailTemplate = emailTemplate::findOrFail($emailTemplate);
        $EmailTemplate->delete();

        return response()->json(null, 204);
    }
}
