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
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\System\User\Group\Removal
 */
class UserGroupRemovalHandler extends SingletonFactory
{
	/**
	 * @var	ObjectType[][]
	 */
	protected $groupedObjectTypes = [];
	
	/**
	 * @param	integer[]		$userIDs
	 */
	public function checkUsers(array $userIDs) {
		if (empty($userIDs)) return;
		
		$userList = new UserList();
		$userList->setObjectIDs($userIDs);
		$userList->readObjects();
		
		$removals = UserGroupRemovalCacheBuilder::getInstance()->getData();
		foreach ($userList as $user) {
			$groupIDs = $user->getGroupIDs();
			$oldGroupID = [];
			
			foreach ($removals as $removal) {
				if (in_array($removal->groupID, $groupIDs) || in_array($removal->groupID, $oldGroupID)) {
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
					'groups' => $oldGroupID
				]);
				$userAction->executeAction();
			}
		}
	}
	
	/**
	 * @return	ObjectType[][]
	 */
	public function getGroupedObjectTypes() {
		return $this->groupedObjectTypes;
	}
	
	/**
	 * @param	UserGroupRemoval	$removal
	 * @param	integer			$maxUsers
	 * @return	User[]
	 */
	public function getUsers(UserGroupRemoval $removal, $maxUsers = null) {
		$userList = new UserList();
		$userList->getConditionBuilder()->add('user_table.userID IN (SELECT userID FROM wcf'.WCF_N.'_user_to_group WHERE groupID = ?)', [
			$removal->groupID
		]);
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
	protected function init() {
		$objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval');
		foreach ($objectTypes as $objectType) {
			if (!$objectType->conditiongroup) continue;
			
			if (!isset($this->groupedObjectTypes[$objectType->conditiongroup])) {
				$this->groupedObjectTypes[$objectType->conditiongroup] = [];
			}
			
			$this->groupedObjectTypes[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
		}
	}
}
