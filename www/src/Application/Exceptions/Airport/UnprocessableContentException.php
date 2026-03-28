<?php

declare(strict_types=1);

namespace App\Application\Exceptions\Airport;

use App\Application\Exceptions\BaseException;

class UnprocessableContentException extends BaseException
{
    protected $message = '{STATUS_CODE_422} Selected airports must not have identical IDs';
}
