<?php
/**
 * AccountTeams Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * AccountTeams class provides access to the AccountTeams object
 *
 * @package surveygizmo-api-php
 */
class AccountTeams
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all of the teams in an account
     *
     * @param bool $showDeleted (optional) Include teams that have been deleted
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $showDeleted = false )
    {
        $_params = http_build_query(array("showdeleted" => $showDeleted));
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
