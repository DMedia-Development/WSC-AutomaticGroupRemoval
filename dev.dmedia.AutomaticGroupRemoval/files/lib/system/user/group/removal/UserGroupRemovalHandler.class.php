<?php

namespace wcf\system\user\group\removal;

use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserList;
use wcf\system\cache\builder\UserGroupRemovalCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Handles user group removal-related matters.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2024 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalHandler extends SingletonFactory
{
    /**
     * list of grouped user group removal condition object types
     * @var ObjectType[][]
     */
    protected $groupedObjectTypes = [];

    /**
     * Checks if the users with the given ids should be removed from
     * any user groups.
     *
     * Note: This method uses the user ids as a parameter instead of user objects
     * on purpose to make sure the latest data of the users are fetched.
     *
     * @param int[] $userIDs
     */
    public function checkUsers(array $userIDs)
    {
        if (empty($userIDs)) {
            return;
        }

        $userList = new UserList();
        $userList->setObjectIDs($userIDs);
        $userList->readObjects();

        /** @var UserGroupRemoval[] $removals */
        $removals = UserGroupRemovalCacheBuilder::getInstance()->getData();
        foreach ($userList as $user) {
            $groupIDs = $user->getGroupIDs();
            $oldGroupID = [];

            foreach ($removals as $removal) {
                if (\in_array($removal->groupID, $groupIDs) || \in_array($removal->groupID, $oldGroupID)) {
                    continue;
                }

                $checkFailed = false;
                $conditions = $removal->getConditions();
                foreach ($conditions as $condition) {
                    if (!$condition->getObjectType()->getProcessor()->checkUser($condition, $user)) {
                        $checkFailed = true;
                        break;
                    }
                }

                if (!$checkFailed) {
                    $oldGroupID[] = $removal->groupID;
                }
            }

            if (!empty($oldGroupID)) {
                $userAction = new UserAction([$user], 'removeFromGroups', [
                    'groups' => $oldGroupID,
                ]);
                $userAction->executeAction();
            }
        }
    }

    /**
     * Returns the list of grouped user group removal condition object types.
     *
     * @return  ObjectType[][]
     */
    public function getGroupedObjectTypes()
    {
        return $this->groupedObjectTypes;
    }

    /**
     * Returns the users who fulfill all conditions of the given user group
     * removal.
     *
     * @param UserGroupRemoval $removal
     * @param int $maxUsers
     * @return  User[]
     */
    public function getUsers(UserGroupRemoval $removal, $maxUsers = null)
    {
        $userList = new UserList();
        $userList->getConditionBuilder()->add(
            'user_table.userID IN (
                SELECT  userID
                FROM    wcf' . WCF_N . '_user_to_group
                WHERE   groupID = ?
            )',
            [$removal->groupID]
        );

        if ($maxUsers !== null) {
            $userList->sqlLimit = $maxUsers;
        }

        $conditions = $removal->getConditions();
        foreach ($conditions as $condition) {
            $condition->getObjectType()->getProcessor()->addUserCondition($condition, $userList);
        }
        $userList->readObjects();

        return $userList->getObjects();
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes(
            'dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval'
        );

        foreach ($objectTypes as $objectType) {
            if (!$objectType->conditiongroup) {
                continue;
            }

            if (!isset($this->groupedObjectTypes[$objectType->conditiongroup])) {
                $this->groupedObjectTypes[$objectType->conditiongroup] = [];
            }

            $this->groupedObjectTypes[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
        }
    }
}
