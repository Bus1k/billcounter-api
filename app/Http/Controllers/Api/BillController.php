<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            Bill::where('user_id', Auth::id())->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'description' => 'required|string|min:3|max:50',
            'amount'      => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'photo'       => 'required|image'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response($validator->errors(), 400);
        }

        $bill = Bill::create([
            'user_id'     => Auth::id(),
            'description' => $request['description'],
            'amount'      => $request['amount'],
            'photo'       => $request->file('photo')->store(env('GOOGLE_DRIVE_FOLDER_ID'))
        ]);

        return response($bill);
    }

    /**
     * Display the specified resource.
     *
     * @param Bill $bill
     */
    public function show(Bill $bill)
    {
        if(Auth::id() === $bill['user_id'])
        {
            return $bill;
        }

        return response(['message' => 'Not Found'], 404);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Bill $bill
     */
    public function update(Request $request, Bill $bill)
    {
        if(Auth::id() === $bill['user_id'])
        {
            $rules = [
                'description' => 'required|string|min:3|max:50',
                'amount'      => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'photo'       => 'required|image'
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                return response($validator->errors(), 400);
            }

            $bill->description = $request['description'];
            $bill->amount      = $request['amount'];
            $bill->photo       = $request->file('photo')->store(env('GOOGLE_DRIVE_FOLDER_ID'));
            $bill->save();

            return response($bill);
        }
        return response(['message' => 'Not Found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bill $bill
     */
    public function destroy(Bill $bill)
    {
        if(Auth::id() === $bill['user_id'])
        {
            $filename = $bill['photo'];

            $dir = '/';
            $recursive = false; // Get subdirectories also?
            $contents = collect(Storage::cloud()->listContents($dir, $recursive));

            $file = $contents
                ->where('type', '=', 'file')
                ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                ->first();

            $result = Storage::cloud()->delete($file['path']);
            if($result)
            {
                $bill->delete();
                return response(['message' => 'Bill deleted successfully.']);
            }
        }
        return response(['message' => 'Not Found'], 404);
    }
}
