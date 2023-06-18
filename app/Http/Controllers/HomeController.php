<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerCsvRequest;
use App\Services\ParseCsvOwnersService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(
        protected ParseCsvOwnersService $parseCsvOwnersService
    ) {}

    public function index(): View
    {
        return view('home');
    }

    public function csvUpload(OwnerCsvRequest $request): JsonResponse
    {
        $csv = $request->file('file');

        $homeOwners = json_encode($this->parseCsvOwnersService->readCsv($csv));

        return response()->json($homeOwners);
    }
}
