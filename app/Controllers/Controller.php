<?php

namespace App\Controllers;

use App\Models\City;

class Controller
{
    protected int $status_code = 200;

    const CODE_STATUS_OK = 200;
    const CODE_WRONG_ARGS = 400;
    const CODE_INTERNAL_ERROR = 500;

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $code): self
    {
        $this->status_code = $code;

        return $this;
    }

    /**
     * @param mixed[] $data 
     */
    public function jsonResponse(array $data): void
    {
        header_remove();
        http_response_code($this->getStatusCode());
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');
        header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With');

        echo json_encode(array_merge(array('status' => $this->getStatusCode() < 300), $data));
    }

    public function index(): void
    {
        $this->setStatusCode(self::CODE_STATUS_OK)->jsonResponse(['data' => City::all()]);
    }

    public function show(int $cityId): void
    {
        $city = City::find($cityId);
        if ($city) {
            $this->setStatusCode(self::CODE_STATUS_OK)->jsonResponse(['data' => $city]);
        } else {
            $this->setStatusCode(self::CODE_INTERNAL_ERROR)->jsonResponse(['error' => "Not found city #$cityId."]);
        }
    }
}
