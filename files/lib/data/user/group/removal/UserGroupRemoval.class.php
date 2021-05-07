<?php
namespace wcf\data\user\group\removal;
use wcf\data\condition\Condition;
use wcf\data\user\group\UserGroup;
use wcf\data\DatabaseObject;
use wcf\system\condition\ConditionHandler;
use wcf\system\request\IRouteController;

/**
 * Represents an automatic removal from a user group.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\Data\User\Group\Removal
 *
 * @property-read   int $removalID       unique id of the automatic user group removal
 * @property-read   int $groupID        id of the user group from which users are automatically removed
 * @property-read   string $title          title of the automatic user group removal
 * @property-read   int $isDisabled     is `1` if the user group removal is disabled and thus not checked for automatic removals, otherwise `0`
 */
class UserGroupRemoval extends DatabaseObject implements IRouteController
{
	/**
	 * @return Condition[]
	 */
	public function getConditions() {
		return ConditionHandler::getInstance()->getConditions('dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval', $this->removalID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @return UserGroup
	 */
	public function getUserGroup() {
		return UserGroup::getGroupByID($this->groupID);
	}
}
