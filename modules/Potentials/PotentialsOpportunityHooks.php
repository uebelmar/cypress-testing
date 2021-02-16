<?php

class PotentialsOpportunityHooks
{
    public function hook_after_retrieve(&$bean, $event, $arguments)
    {
        $bean->opportunitypotentials = [];
        $linkedPotentials = $bean->get_linked_beans('potentials', 'Potential');
        foreach ($linkedPotentials as $linkedPotential) {
            $bean->opportunitypotentials[] = [
                'id' => $linkedPotential->id,
                'opportunity_amount' => $linkedPotential->opportunity_amount,
                'opportunity_amount_usdollar' => $linkedPotential->opportunity_amount_usdollar
            ];
        }

        $bean->opportunitypotentials = json_encode($bean->opportunitypotentials);
    }

    public function hook_after_save(&$bean, $event, $arguments)
    {
        $opportunityPotentials = json_decode($bean->opportunitypotentials);

        $bean->load_relationship('potentials');
        $linkedPotentials = $bean->get_linked_beans('potentials', 'Potential');

        foreach ($linkedPotentials as $linkedPotential) {
            // check if we have this in the array
            $i = 0;
            $potentialActive = false;
            foreach ($opportunityPotentials as $opportunityPotential) {
                if ($opportunityPotential->id == $linkedPotential->id) {
                    $bean->db->query("UPDATE " . $bean->potentials->relationship->getRelationshipTable() . " SET amount='{$opportunityPotential->opportunity_amount}' WHERE opportunity_id='{$bean->id}' AND potential_id='{$linkedPotential->id}' AND deleted=0");
                    unset($opportunityPotentials[$i]);
                    $potentialActive = true;
                    break;
                }
                $i++;
            }

            // delete the relationship if we have not found it
            if (!$potentialActive)
                $bean->potentials->delete($bean->id, $linkedPotential->id);
        }

        foreach ($opportunityPotentials as $opportunityPotential) {
            $bean->potentials->add($opportunityPotential->id, ['amount' => $opportunityPotential->opportunity_amount]);
        }

    }
}