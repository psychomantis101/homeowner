<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerCsvRequest;
use App\Services\ParseCsvOwnersService;

class HomeController extends Controller
{
    public function __construct(
        protected ParseCsvOwnersService $parseCsvOwnersService
    ) {}

    public function index()
    {
        return view('home');
    }

    public function csvUpload(OwnerCsvRequest $request)
    {
        $file = $request->file('file');

        $homeOwners = json_encode($this->readCSV($file));

        return response()->json($homeOwners);
    }

    protected function readCSV($csv)
    {
        $file = fopen($csv, "r");

        fgetcsv($file);

        $homeOwners = [];

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            $homeOwners[] = $this->parseCsvOwnersService->parseOwners($row[0]);
        }

        fclose($file);

        return $homeOwners;
    }
}
