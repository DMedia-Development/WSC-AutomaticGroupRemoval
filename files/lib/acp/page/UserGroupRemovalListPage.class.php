<?php

namespace wcf\acp\page;

use wcf\data\user\group\removal\UserGroupRemovalList;
use wcf\page\MultipleLinkPage;

/**
 * Lists the available automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\Acp\Page
 *
 * @property    UserGroupRemovalList $objectList
 */
class UserGroupRemovalListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.removal';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageGroupAssignment'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = UserGroupRemovalList::class;
}
