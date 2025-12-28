<?php

namespace App\Enums;

enum ApiResponseType: string
{
    case Success  = 'success';
    case Error    = 'error';
    case Info     = 'info';
    case Warning  = 'warning';
}
