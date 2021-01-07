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
}
