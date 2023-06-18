<?php

namespace App\Services;

class ParseCsvOwnersService
{
    public function readCsv($csv): array
    {
        $file = fopen($csv, "r");

        fgetcsv($file);

        $homeOwners = [];

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            $homeOwners[] = $this->parseOwners($row[0]);
        }

        fclose($file);

        return $homeOwners;
    }
    private function parseOwners($owners): array
    {
        $parsedOwners = [];

        //remove unwanted periods that are not part of initials
        $owners = str_replace('.', '', $owners);

        //split into the multiple users if there are more than one
        $owners = preg_split("/(&|and)/i", $owners);
        $owners = array_map('trim', $owners);

        //parse each owner
        foreach ($owners as $owner) {
            $parsedOwners[] = $this->parseOwner($owner, $owners[1] ?? '');
        }

        return $parsedOwners;
    }

    private function parseOwner($owner, $secondOwner): array
    {
        //split on whitespace to get the elements
        $ownerElements = explode(' ', $owner);

        //title is always the first element
        $parsedOwner = [
            'title' => $ownerElements[0],
            'first_name' => null,
            'initial' => null
        ];

        //if there are 2 owners, the main information is often stored on the second owner I.E Mr and Mrs Smith
        $loopElements = count($ownerElements) > 1 ? $ownerElements : explode(' ', $secondOwner);

        //remove title as its already been stored
        array_shift($loopElements);

        foreach ($loopElements as $key => $loopElement) {
            //if the element is one character long, it is an initial
            if (strlen($loopElement) === 1) {
                $parsedOwner['initial'] = $loopElement;
                continue;
            }

            //if the element is the last element, it is the last name
            if ($key === array_key_last($loopElements)) {
                $parsedOwner['last_name'] = $loopElement;
                break;
            }

            //anything left is the first name
            $parsedOwner['first_name'] = $loopElement;
        }

        return $parsedOwner;
    }
}
