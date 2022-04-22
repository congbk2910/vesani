<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

interface EntityDataSenderInterface
{
    /**
     * @param $data
     * @return string|null
     */
    public function send($data);
}
