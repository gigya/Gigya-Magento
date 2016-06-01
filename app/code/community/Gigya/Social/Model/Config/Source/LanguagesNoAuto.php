<?php

class Gigya_Social_Model_Config_Source_LanguagesNoAuto
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $langs = new Gigya_Social_Model_Config_Source_Languages();
        $langArray = $langs->toOptionArray();
        array_shift($langArray);
        return $langArray;
    }
}
