<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ImagesHelper;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Repositories\BillRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    private BillRepository $repository;

    public function __construct(BillRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return response(
            $this->repository->all()
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

        $fileName = $request->file('photo')->store(env('GOOGLE_DRIVE_FOLDER_ID'));

        if($fileName)
        {
            // Change file permissions
            $photo = ImagesHelper::getGoogleImage($fileName);
            ImagesHelper::changeImagePermission($photo);

            $bill = $this->repository->create(
                $request['description'],
                $request['amount'],
                $fileName,
                Storage::cloud()->url($photo['path'])
            );

            return response($bill);
        }

        return response(['message' => 'Problem with saving the bill'], 400);
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
                'photo'       => 'image'
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                return response($validator->errors(), 400);
            }

            if($request->file('photo'))
            {
                $photo = ImagesHelper::getGoogleImage($bill['photo_name']);

                if(Storage::cloud()->delete($photo['path']))
                {
                    $bill->photo_name = $request->file('photo')->store(env('GOOGLE_DRIVE_FOLDER_ID'));
                    $photo = ImagesHelper::getGoogleImage($bill->photo_name);
                    ImagesHelper::changeImagePermission($photo);
                    $bill->photo_url  = Storage::cloud()->url($photo['path']);
                }
            }

            $bill->description = $request['description'];
            $bill->amount      = $request['amount'];
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
            $photo = ImagesHelper::getGoogleImage($bill['photo_name']);

            $result = Storage::cloud()->delete($photo['path']);
            if($result)
            {
                $bill->delete();
                return response(['message' => 'Bill deleted successfully.']);
            }
        }
        return response(['message' => 'Not Found'], 404);
    }


}
