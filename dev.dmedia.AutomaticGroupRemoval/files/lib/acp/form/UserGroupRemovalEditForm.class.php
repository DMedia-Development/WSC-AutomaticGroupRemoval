<?php

namespace wcf\acp\form;

use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\data\user\group\removal\UserGroupRemovalAction;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the form to edit an existing automatic user group removal.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2024 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalEditForm extends UserGroupRemovalAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.removal';

    /**
     * edited automatic user group removal
     */
    public UserGroupRemoval $removal;

    /**
     * id of the edited automatic user group removal
     */
    public int $removalID = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'action' => 'edit',
            'removal' => $this->removal,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        if (empty($_POST)) {
            $this->groupID = $this->removal->groupID;
            $this->title = $this->removal->title;

            $conditions = $this->removal->getConditions();
            foreach ($conditions as $condition) {
                $conditionGroupConditions = $this->conditions[$condition->getObjectType()->conditiongroup];
                $conditionGroupConditions[$condition->objectTypeID]->getProcessor()->setData($condition);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->removalID = \intval($_REQUEST['id']);
        }

        $this->removal = new UserGroupRemoval($this->removalID);
        if (!$this->removal->removalID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $this->objectAction = new UserGroupRemovalAction([$this->removal], 'update', [
            'data' => \array_merge($this->additionalFields, [
                'groupID' => $this->groupID,
                'isDisabled' => $this->isDisabled,
                'title' => $this->title,
            ]),
        ]);
        $this->objectAction->executeAction();

        // transform conditions array into one-dimensional array
        $conditions = [];
        foreach ($this->conditions as $groupedObjectTypes) {
            $conditions = \array_merge($conditions, $groupedObjectTypes);
        }

        ConditionHandler::getInstance()->updateConditions(
            $this->removal->removalID,
            $this->removal->getConditions(),
            $conditions
        );

        $this->saved();

        WCF::getTPL()->assign('success', true);
    }
}
