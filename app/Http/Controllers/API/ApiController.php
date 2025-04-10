<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

class ApiController extends Controller
{
    use ApiResponser;

    public static function getLoginKey($user_id)
    {
        $salt = "23df$#%%^66sd$^%fg%^sjgdk90fdklndg099ndfg09LKJDJ*@##lkhlkhlsa#$%";
        $login_key = hash('sha1', $salt . $user_id . time());
        return $login_key;
    }

    public function pagination($data, $page, $limit)
    {
        $totalRecords = count($data);
        $start = ($page - 1) * $limit;
        $end = $start + $limit;
        // echo "Total Records : " . $totalRecords . "\n";
        // echo "Start : " . $start . "\n";
        // echo "End : " . $end . "\n";
        $paginatedData = array_slice($data, $start, $limit);

        $hasMore = $end < $totalRecords;

        return [
            'data' => $paginatedData,
            'hasMore' => $hasMore,
        ];
    }
}
