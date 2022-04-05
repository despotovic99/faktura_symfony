<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class StampanjeException extends Exception {
    #[Pure] public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        parent::__toString(); // TODO: Change the autogenerated stub
    }


}