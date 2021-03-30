<?php
namespace wcf\data\user\group\removal;
use wcf\data\condition\Condition;
use wcf\data\user\group\UserGroup;
use wcf\data\DatabaseObject;
use wcf\system\condition\ConditionHandler;
use wcf\system\request\IRouteController;

class UserGroupRemoval extends DatabaseObject implements IRouteController {
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
