<?php
/**
 * SurveyOption Object
 *
 * @package surveygizmo-api-php
 * @version 0.3
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;


/**
 * SurveyOption class provides access to the SurveyOption object
 *
 * @package surveygizmo-api-php
 */
class SurveyOption
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all the options in a question
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question to get options for
     * @param string|int $page (optional) page of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $surveyId, $questionId, $page = 1 )
    {
        $page = ($page) ? $page : 1;
        $_params = http_build_query(array("page" => $page));
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption', 'GET', $_params);
    }

    /**
     * Get a specific option in a question
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question containing option
     * @param string|int $optionSku Sku of option to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getOption( $surveyId, $questionId, $optionSku )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption/' . $optionSku, 'GET');
    }
}

