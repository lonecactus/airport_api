<?php

declare(strict_types=1);

namespace App\Application\Exceptions\Airport;

use App\Application\Exceptions\BaseException;

class NotFoundException extends BaseException
{
    protected $message = '{STATUS_CODE_404} The item you requested does not exist.';
}
