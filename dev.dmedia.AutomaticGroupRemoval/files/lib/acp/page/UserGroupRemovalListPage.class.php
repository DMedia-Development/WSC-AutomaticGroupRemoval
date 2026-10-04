<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\UserGroupRemovalGridView;

/**
 * Lists the available automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<UserGroupRemovalGridView>
 */
final class UserGroupRemovalListPage extends AbstractGridViewPage
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
    #[\Override]
    protected function createGridView(): UserGroupRemovalGridView
    {
        return new UserGroupRemovalGridView();
    }
}
