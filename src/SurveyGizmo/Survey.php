<?php
/**
 * Survey Object
 *
 * @package surveygizmo-api-php
 * @version 0.3
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

class Survey
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List all the surveys in an account
	 *
     * @param page string|int optional page of results to fetch
	 * @param limit string|int optional number of results to fetch
     * @param filter array optional one or more filter arrays
	 * @return SG API object
     */
    public function getList( $page = 1, $limit = 50, $filter = array() )
	{
		$page = ($page) ? $page : 1;
		$limit = ($limit) ? $limit : 50;
        $_params = http_build_query(array("resultsperpage" => $limit, "page" => $page));
		if ($filter) $_params .= "&" . $this->master->getFilterString($filter);
        return $this->master->call('survey', 'GET', $_params);
    }
	
    /**
     * Get a specific survey
	 *
     * @param surveyId string|int survey to fetch
	 * @return SG API object
     */
    public function getSurvey( $surveyId )
	{
        return $this->master->call('survey/' . $surveyId, 'GET');
    }
	
	/**
	 * Create a new survey
	 *
	 * @param title string title of new survey
	 * @param type string type of new survey
	 * @param parameters array key-value pairs of additional parameters
	 */
	public function createSurvey( $title, $type, $parameters = array() )
	{
		$parameters["title"] = $title;
		$parameters["type"] = $type;
		$allowed_params = array
		("title", "type", "status", "theme", "team", "options[internal_title]", "blockby", "polloptions", "polltype", "pollwidth");
		
		foreach ($parameters as $key => $value) {
			if(!in_array($key, $allowed_params)) {
				unset($parameters[$key]);
			}
		}
        $_params = http_build_query($parameters);
        return $this->master->call('survey/', 'PUT', $_params);
	}
	
}

