<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

// CR1000333
namespace SpiceCRM\modules\SystemDeploymentReleases\guideChecks;

class SystemDeploymentReleaseGuideChecks {

    /**
     * Write a short description for the release:
     * which 2 important achievements will be released?
     * @param $release
     * @return bool
     */
    public function descriptionIsSet($release)
    {
        if(!empty($release->description))
            return true;

        return false;
    }

    /**
     * All change requests linked to the release have crstatus 'unit tested' or "canceled/deferred"
     * This is required before release may be set to 'testing'
     * If no change requests found at all then no need for release.
     * @param $release
     * @return bool
     */
    public function allRelatedCRsUnitTested($release)
    {
        $changerequests = $release->get_linked_beans('systemdeploymentcrs', 'SystemDeploymentCR', array(),0, -1, 0, "");
        if(count($changerequests) <= 0)
            return false;

        foreach($changerequests as $cr){
            if($cr->crstatus != '2' && $cr->crstatus != '5') // see crstatus_dom
                return false;
        }

        return true;
    }

    /**
     * All change requests linked to the release have crstatus 'completed' or 'canceled/deferred'
     * This is required before release may be set to 'release'
     * @param $release
     * @return bool
     */
    public function allRelatedCRsCompleted($release)
    {
        $changerequests = $release->get_linked_beans('systemdeploymentcrs', 'SystemDeploymentCR', array(),0, -1, 0, "");
        if(count($changerequests) <= 0)
            return false;

        foreach($changerequests as $cr){
            if($cr->crstatus != '4' && $cr->crstatus != '5') // see crstatus_dom
                return false;
        }

        return true;
    }

    /**
     * @param $release
     * @return bool
     */
    public function plannedCompletionDateIsSet($release)
    {
        if(!empty($release->planned_date_release_closed))
            return true;

        return false;
    }

    /**
     * define users who will take over technical part of release
     * @param $release
     * @return bool
     */
    public function techTeamIsDefined($release) {
        $users = $release->get_linked_beans('users', 'User', array(),0, -1, 0, "");
        if(!$users)
            return false;
        if(count($users) > 0)
            return true;

        return false;
    }



}