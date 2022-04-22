<?php

namespace Omnisend\Omnisend\Helper;

class GenderHelper
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const GENDER_STRING_MALE = 'm';
    const GENDER_STRING_FEMALE = 'f';

    /**
     * @param int $gender
     * @return null|string
     */
    public function getGenderString($gender)
    {
        switch ($gender) {
            case self::GENDER_MALE:
                return self::GENDER_STRING_MALE;
            case self::GENDER_FEMALE:
                return self::GENDER_STRING_FEMALE;
        }

        return null;
    }
}
