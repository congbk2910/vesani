<?php
namespace Svea\Checkout\Model\Client\DTO;



abstract class AbstractRequest
{
    // do stuff
    public abstract function toJSON();
    public abstract function toArray();
}