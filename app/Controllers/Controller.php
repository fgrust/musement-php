<?php

namespace App\Controllers;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;

class Controller
{
    protected $status_code = 200;

    const CODE_STATUS_OK = 200;
    const CODE_WRONG_ARGS = 400;
    const CODE_INTERNAL_ERROR = 500;

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function setStatusCode($code)
    {
        $this->status_code = $code;

        return $this;
    }

    public function jsonResponse(array $data)
    {
        header_remove();
        http_response_code($this->status_code);
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');
        header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With');

        echo json_encode(array_merge(array('status' => $this->status_code < 300), $data));
    }

    public function index()
    {
        return $this->setStatusCode(self::CODE_STATUS_OK)->jsonResponse(['data' => City::all()]);
    }

    public function show($city_id)
    {
        $city = City::find($city_id);
        if ($city) {
            return $this->setStatusCode(self::CODE_STATUS_OK)->jsonResponse(['data' => $city]);
        }
        return $this->setStatusCode(self::CODE_INTERNAL_ERROR)->jsonResponse(['error' => "Not found city #$city_id."]);
    }
}
