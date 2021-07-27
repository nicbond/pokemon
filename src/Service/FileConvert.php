<?php

namespace App\Service;

class FileConvert
{
    public function convertToArray($filepath): array 
    {
        $lines = array();
        $line = 0;

        if (($handle = fopen($filepath, "r")) !== FALSE) {
            $firstLine = true;
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $column = count($data);
                if ($firstLine) {
                    $firstLine = false;
                    continue;
                }
                $line++;
                $lines[] = $data;
            }
        }
        return $lines;
    }
}