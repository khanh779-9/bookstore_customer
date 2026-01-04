<?php
use Shuchkin\SimpleXLSXGen;

require_once __DIR__ . '/SimpleXLSXGen/SimpleXLSXGen.php';

class Xlsx_Helper{
    public static function array_to_xlsx_download($data, $filename = 'data.xlsx'){
        $safeName = preg_replace('/[^A-Za-z0-9_\-\.]+/', '_', $filename ?: 'data.xlsx');
        $lower = strtolower($safeName);
        if (substr($lower, -5) !== '.xlsx') {
            $safeName .= '.xlsx';
        }

        $xlsx = SimpleXLSXGen::fromArray($data);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        $xlsx->downloadAs($safeName);
        exit;
    }
}

?>