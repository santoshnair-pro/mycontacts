<?php

namespace App\util;

final class Response
{
    private int $code; // 200 for success and others for error
    private string $status; // Error or Success

    private array $data = [];

    public function __construct($code, $status, $data)
    {
        $this->code   = $code;
        $this->status = $status;
        $this->data   = $data;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function toJson()
    {
        header('Content-Type: application/json');
        $data = [
            'code'   => $this->code,
            'status' => $this->status,
            'data'   => $this->data,
        ];
        echo json_encode($data);
    }
}
