<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\UserAction;
use wcf\system\cache\builder\UserGroupRemovalCacheBuilder;
use wcf\system\user\group\removal\UserGroupRemovalHandler;

/**
 * Executes automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\System\Cronjob
 */
class UserGroupRemovalCronjob extends AbstractCronjob
{
	const MAXIMUM_REMOVALS = 1000;
	
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob)
	{
		parent::execute($cronjob);
		
		$removals = UserGroupRemovalCacheBuilder::getInstance()->getData();
		$usersFromGroup = [];
		
		$removalCount = 0;
		foreach ($removals as $removal) {
			if (!isset($usersFromGroup[$removal->groupID])) {
				$usersFromGroup[$removal->groupID] = [];
			}
			
			$newUsers = UserGroupRemovalHandler::getInstance()->getUsers($removal, self::MAXIMUM_REMOVALS);
			$usersFromGroup[$removal->groupID] = \array_merge($usersFromGroup[$removal->groupID], $newUsers);
			
			$removalCount += \count($newUsers);
			if ($removalCount > self::MAXIMUM_REMOVALS) {
				break;
			}
		}
		
		foreach ($usersFromGroup as $groupID => $users) {
			if (!empty($users)) {
				$userAction = new UserAction(\array_unique($users), 'removeFromGroups', [
					'groups' => [$groupID]
				]);
				$userAction->executeAction();
			}
		}
	}
}