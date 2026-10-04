<?php

namespace wcf\system\cache\builder;

use wcf\data\user\group\removal\UserGroupRemovalList;

/**
 * Caches the enabled automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function rebuild(array $parameters)
    {
        $removalList = new UserGroupRemovalList();
        $removalList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $removalList->readObjects();

        return $removalList->getObjects();
    }
}
