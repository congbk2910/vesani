<?php


namespace Svea\Checkout\Model\Svea;


class Locale
{

    /**
     * Swedish, Norway, Danish Kronor
     * @var array $allowedCurrencies
     */
    protected $allowedCurrencies = [
      "SEK","NOK","DKK","EUR"
    ];


    protected $allowedCountries = [
        "SE","NO","DK","FI"
    ];

    protected $locales = [
        "SE" => [
            "locale" => "sv-SE",
            "currency" => "SEK",
            "test" => [
                "EmailAddress" => "test@example.com",
                "PhoneNumber" => "0811111111",
                "PostalCode" => "99999",
            ]
        ],
        "NO" => [
            "locale" => "nn-NO",
            "currency" => "NOK",
            "test" => [
                "EmailAddress" => "test@example.com",
                "PhoneNumber" => "21222222",
                "PostalCode" => "0359",
            ]
        ],
        "DK" => [
            "locale" => "da-DK",
            "currency" => "DKK",
            "test" => [
                "EmailAddress" => "test@example.com",
                "PhoneNumber" => "22222222",
                "PostalCode" => "2100",
            ]
        ],
        "FI" => [
            "locale" => "fi-FI",
            "currency" => "EUR",
            "test" => [
                "EmailAddress" => "test@example.com",
                "PostalCode" => "370",
            ]
        ],
    ];


    /**
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->allowedCurrencies;
    }

    /**
     * @return array
     */
    public function getAllowedCountries()
    {
        return $this->allowedCountries;
    }

    /**
     * @param $countryCode string
     * @return string
     */
    public function getLocaleByCountryCode($countryCode)
    {
        if (array_key_exists($countryCode, $this->locales)) {
            return $this->locales[$countryCode]['locale'];
        }

        return "en-UK";
    }

    /**
     * @param $countryCode string
     * @return array
     */
    public function getTestPresetValuesByCountryCode($countryCode)
    {
        if (isset($this->locales[$countryCode]['test'])) {
            return $this->locales[$countryCode]['test'];
        }

        return [];
    }

    public function isValidCurrency($countryCode, $currency)
    {
        if (!array_key_exists($countryCode, $this->locales)) {
            return false;
        }

        return $this->locales[$countryCode]['currency'] === strtoupper($currency);
    }

    public function getCurrencyByCountryCode($countryCode)
    {
        if (!array_key_exists($countryCode, $this->locales)) {
            return null;
        }

        return $this->locales[$countryCode]['currency'];
    }
}