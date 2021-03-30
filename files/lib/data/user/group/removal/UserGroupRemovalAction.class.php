<?php
namespace wcf\data\user\group\removal;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\system\condition\ConditionHandler;

class UserGroupRemovalAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.user.canManageGroupAssignment'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.user.canManageGroupAssignment'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update'];
	
	/**
	 * @inheritDoc
	 */
	public function delete() {
		ConditionHandler::getInstance()->deleteConditions('dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval', $this->objectIDs);
		
		return parent::delete();
	}
	
	/**
	 * @inheritDoc
	 */
	public function toggle() {
		foreach ($this->getObjects() as $removal) {
			$removal->update([
				'isDisabled' => $removal->isDisabled ? 0 : 1
			]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
}
