<?php
namespace App\Repositories;

use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class BillRepository
{
    private Bill $billModel;

    public function __construct(Bill $billModel)
    {
        $this->billModel = $billModel;
    }

    public function all()
    {
        return $this->billModel->where('user_id', Auth::id())->get();
    }

    public function create(string $description, float $amount, string $fileName, string $url)
    {
        return $this->billModel->create([
            'user_id'     => Auth::id(),
            'description' => $description,
            'amount'      => $amount,
            'photo_name'  => $fileName,
            'photo_url'   => $url
        ]);
    }
}
