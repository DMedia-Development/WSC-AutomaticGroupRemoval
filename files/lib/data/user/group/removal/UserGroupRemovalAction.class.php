<?php

namespace wcf\data\user\group\removal;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\data\TDatabaseObjectToggle;
use wcf\system\condition\ConditionHandler;

/**
 * Executes user group removal-related actions.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\Data\User\Group\Removal
 *
 * @method  UserGroupRemoval     create()
 * @method  UserGroupRemovalEditor[] getObjects()
 * @method  UserGroupRemovalEditor   getSingleObject()
 */
class UserGroupRemovalAction extends AbstractDatabaseObjectAction implements IToggleAction
{
    use TDatabaseObjectToggle;

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.user.canManageGroupAssignment'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.user.canManageGroupAssignment'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'toggle', 'update'];

    /**
     * @inheritDoc
     */
    public function delete()
    {
        ConditionHandler::getInstance()->deleteConditions(
            'dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval',
            $this->objectIDs
        );

        return parent::delete();
    }
}
