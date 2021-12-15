<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCategory(Request $request)
    {
        $input = $request->all();

        $categories = Category::where('type_id',$input['type_id'])
            ->where('status',"1")
            ->get();
       
        return $this->generateResponse(true,'Category List',$categories,200);
    }
}
