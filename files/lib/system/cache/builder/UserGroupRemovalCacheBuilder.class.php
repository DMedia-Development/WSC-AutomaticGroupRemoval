<?php

namespace wcf\system\cache\builder;

use wcf\data\user\group\removal\UserGroupRemovalList;

/**
 * Caches the enabled automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\System\Cache\Builder
 */
class UserGroupRemovalCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $removalList = new UserGroupRemovalList();
        $removalList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $removalList->readObjects();

        return $removalList->getObjects();
    }
}
