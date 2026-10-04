<?php

namespace wcf\system\interaction\admin;

use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\event\interaction\admin\UserGroupRemovalInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\WCF;

/**
 * Interaction provider for automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class UserGroupRemovalInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        if (!WCF::getSession()->getPermission('admin.user.canManageGroupAssignment')) {
            return;
        }

        $this->addInteractions([
            new DeleteInteraction('core/users/groups/removals/%s'),
        ]);

        EventHandler::getInstance()->fire(
            new UserGroupRemovalInteractionCollecting($this)
        );
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getObjectClassName(): string
    {
        return UserGroupRemoval::class;
    }
}
