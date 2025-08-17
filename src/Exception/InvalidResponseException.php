<?php

namespace Moco\Exception;

/**
 * Invalid Response exception.
 *
 * Thrown when a gateway responded with invalid or unexpected data (for example, a security hash did not match).
 */
class InvalidResponseException extends \Exception
{
}
