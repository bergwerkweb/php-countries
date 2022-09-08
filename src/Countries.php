<?php

namespace RapidWeb\Countries;

use RapidWeb\Countries\DataSources\MledozeCountriesJson;
use RapidWeb\Countries\Interfaces\DataSourceInterface;

class Countries
{
    public $dataSource;

    public function __construct()
    {
        $this->setDataSource(new MledozeCountriesJson());
    }

    public function setDataSource(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function all()
    {
        $countries = $this->dataSource->all();

        usort($countries, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $countries;
    }

    public function getByName(string $name, string $separator=',')
    {
        $result = [];
        $countries_name = explode($separator, $name);
        $countries_name = array_map('trim', $countries_name);
        foreach ($this->all() as $country) {
            if (in_array($country->name, $countries_name) || in_array($country->officialName, $countries_name) || !empty(array_intersect($countries_name, $country->altSpellings))){
                $result[$country->isoCodeAlpha2] = $country;
            }
        }
        return $result;
    }

    public function getByIsoCode(string $code)
    {
        foreach ($this->all() as $country) {
            if ($country->isoCodeAlpha2 == $code || $country->isoCodeAlpha3 == $code || $country->isoCodeNumeric == $code) {
                return $country;
            }
        }
    }

    public function getByLanguage(string $language)
    {
        $countries = [];

        foreach ($this->all() as $country) {
            foreach ($country->languages as $countryLanguage) {
                if ($countryLanguage == $language) {
                    $countries[] = $country;
                }
            }
            foreach ($country->languageCodes as $countryLanguageCode) {
                if ($countryLanguageCode == $language) {
                    $countries[] = $country;
                }
            }
        }

        return $countries;
    }
}
