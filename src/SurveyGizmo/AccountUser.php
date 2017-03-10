<?php
/**
 * AccountUser Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * AccountUser class provides access to the AccountUser object
 *
 * @package surveygizmo-api-php
 */
class AccountUser
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all of the teams in an account
     *
     * @param int $page (optional) page of results to fetch
     * @param int $limit (optional) number of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $page = 1, $limit = 50 )
    {
        $page = ($page) ? $page : 1;
        $limit = ($limit) ? $limit : 50;
        $_params = http_build_query(array("resultsperpage" => $limit, "page" => $page));
        return $this->master->call('accountteams/', 'GET', $_params);
    }

    /**
     * Get information about a specific team
     *
     * @param int $teamId Id of team to get information for
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getTeam( $teamId )
    {
        return $this->master->call('accountteams/' . $teamId, 'GET');
    }

    /**
     * Create a new team
     *
     * @param string $teamName Name of new team
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createTeam( $teamName, $parameters = array() )
    {
        $parameters["teamname"] = $teamName;
        $allowed_params = array("teamname", "description", "color", "defaultrole");
        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('accountteams/', 'PUT', $_params);
    }

    /**
     * Update a specified team
     *
     * @param int $teamId Id of team to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateTeam( $teamId, $parameters = array() )
    {
        $allowed_params = array("teamname", "description", "color", "defaultrole");
        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('accountteams/' . $teamId, 'POST', $_params);
    }

    /**
     * Delete a specified team
     *
     * @param int $teamId Id of team to update
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteTeam( $teamId )
    {
        return $this->master->call('accountteams/' . $teamId, 'DELETE');
    }
}
