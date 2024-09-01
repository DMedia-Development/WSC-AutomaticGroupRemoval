<?php

namespace wcf\data\user\group\removal;

use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\builder\ConditionCacheBuilder;
use wcf\system\cache\builder\UserGroupRemovalCacheBuilder;

/**
 * Executes user group removal-related actions.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2024 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method static UserGroupRemoval create(array $parameters = [])
 * @method      UserGroupRemoval getDecoratedObject()
 * @mixin       UserGroupRemoval
 */
class UserGroupRemovalEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserGroupRemoval::class;

    /**
     * @inheritDoc
     */
    public static function resetCache()
    {
        UserGroupRemovalCacheBuilder::getInstance()->reset();
        ConditionCacheBuilder::getInstance()->reset([
            'definitionID' => ObjectTypeCache::getInstance()->getDefinitionByName(
                'dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval'
            )->definitionID,
        ]);
    }
}
