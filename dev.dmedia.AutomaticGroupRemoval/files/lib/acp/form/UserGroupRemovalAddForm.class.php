<?php

namespace wcf\acp\form;

use wcf\data\object\type\ObjectType;
use wcf\data\user\group\removal\UserGroupRemovalAction;
use wcf\data\user\group\UserGroup;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\user\group\removal\UserGroupRemovalHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the form to create a new automatic user group removal.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2024 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.removal.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageGroupAssignment'];

    /**
     * list of grouped user group removal condition object types
     * @var ObjectType[][]
     */
    public $conditions = [];

    /**
     * id of the selected user group
     */
    public int $groupID = 0;

    /**
     * true if the automatic removal is disabled
     */
    public int $isDisabled = 0;

    /**
     * title of the user group removal
     */
    public string $title = '';

    /**
     * list of selectable user groups
     * @var UserGroup[]
     */
    public $userGroups = [];

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'action' => 'add',
            'groupedObjectTypes' => $this->conditions,
            'groupID' => $this->groupID,
            'isDisabled' => $this->isDisabled,
            'title' => $this->title,
            'userGroups' => $this->userGroups,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        $this->userGroups = UserGroup::getSortedGroupsByType([], [
            UserGroup::EVERYONE,
            UserGroup::GUESTS,
            UserGroup::OWNER,
            UserGroup::USERS,
        ]);

        foreach ($this->userGroups as $key => $userGroup) {
            if (!$userGroup->isAccessible()) {
                unset($this->userGroups[$key]);
            }

            // also exlude groups with ACP access
            if ($userGroup->getGroupOption('admin.general.canUseAcp')) {
                unset($this->userGroups[$key]);
            }
        }

        $this->conditions = UserGroupRemovalHandler::getInstance()->getGroupedObjectTypes();

        parent::readData();
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_POST['groupID'])) {
            $this->groupID = \intval($_POST['groupID']);
        }

        if (isset($_POST['isDisabled'])) {
            $this->isDisabled = 1;
        }

        if (isset($_POST['title'])) {
            $this->title = StringUtil::trim($_POST['title']);
        }

        foreach ($this->conditions as $conditions) {
            /** @var ObjectType $condition */
            foreach ($conditions as $condition) {
                $condition->getProcessor()->readFormParameters();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        $this->objectAction = new UserGroupRemovalAction([], 'create', [
            'data' => \array_merge($this->additionalFields, [
                'groupID' => $this->groupID,
                'isDisabled' => $this->isDisabled,
                'title' => $this->title,
            ]),
        ]);
        $returnValues = $this->objectAction->executeAction();

        // transform conditions array into one-dimensional array
        $conditions = [];
        foreach ($this->conditions as $groupedObjectTypes) {
            $conditions = \array_merge($conditions, $groupedObjectTypes);
        }

        ConditionHandler::getInstance()->createConditions($returnValues['returnValues']->removalID, $conditions);

        $this->saved();

        // reset values
        $this->groupID = 0;
        $this->isDisabled = 0;
        $this->title = '';

        foreach ($this->conditions as $conditions) {
            foreach ($conditions as $condition) {
                $condition->getProcessor()->reset();
            }
        }

        WCF::getTPL()->assign([
            'success' => true,
            'objectEditLink' => LinkHandler::getInstance()->getControllerLink(
                UserGroupRemovalEditForm::class,
                ['id' => $returnValues['returnValues']->removalID]
            ),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        if (empty($this->title)) {
            throw new UserInputException('title');
        }

        if (\strlen($this->title) > 255) {
            throw new UserInputException('title', 'tooLong');
        }

        if (!isset($this->userGroups[$this->groupID])) {
            throw new UserInputException('groupID', 'noValidSelection');
        }

        $hasData = false;
        foreach ($this->conditions as $conditions) {
            foreach ($conditions as $condition) {
                $condition->getProcessor()->validate();

                if (!$hasData && $condition->getProcessor()->getData() !== null) {
                    $hasData = true;
                }
            }
        }

        if (!$hasData) {
            throw new UserInputException('conditions');
        }
    }
}
