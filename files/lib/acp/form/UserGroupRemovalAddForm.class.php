<?php
namespace wcf\acp\form;
use wcf\data\object\type\ObjectType;
use wcf\data\user\group\removal\UserGroupRemovalAction;
use wcf\data\user\group\UserGroup;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\user\group\removal\UserGroupRemovalHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the form to create a new automatic user group removal.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2021 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package WoltLabSuite\Core\Acp\Form
 */
class UserGroupRemovalAddForm extends AbstractForm
{
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.group.removal.add';
	
	/**
	 * @var	ObjectType[][]
	 */
	public $conditions = [];
	
	/**
	 * @var	integer
	 */
	public $groupID = 0;
	
	/**
	 * @var	integer
	 */
	public $isDisabled = 0;
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.user.canManageGroupAssignment'];
	
	/**
	 * @var	string
	 */
	public $title = '';
	
	/**
	 * @var	UserGroup[]
	 */
	public $userGroups = [];
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'action' => 'add',
			'groupedObjectTypes' => $this->conditions,
			'groupID' => $this->groupID,
			'isDisabled' => $this->isDisabled,
			'title' => $this->title,
			'userGroups' => $this->userGroups
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		$this->userGroups = UserGroup::getGroupsByType([], [
			UserGroup::EVERYONE,
			UserGroup::GUESTS,
			UserGroup::USERS,
			UserGroup::OWNER
		]);

		foreach ($this->userGroups as $key => $userGroup) {
			if (!$userGroup->isAccessible()) {
				unset($this->userGroups[$key]);
			}

			if ($userGroup->getGroupOption('admin.general.canUseAcp')) {
				unset($this->userGroups[$key]);
			}
		}
		
		uasort($this->userGroups, function(UserGroup $groupA, UserGroup $groupB) {
			return strcmp($groupA->getName(), $groupB->getName());
		});
		
		$this->conditions = UserGroupRemovalHandler::getInstance()->getGroupedObjectTypes();
		
		parent::readData();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['groupID'])) $this->groupID = intval($_POST['groupID']);
		if (isset($_POST['isDisabled'])) $this->isDisabled = 1;
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->readFormParameters();
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		$this->objectAction = new UserGroupRemovalAction([], 'create', [
			'data' => array_merge($this->additionalFields, [
				'groupID' => $this->groupID,
				'isDisabled' => $this->isDisabled,
				'title' => $this->title
			])
		]);
		$returnValues = $this->objectAction->executeAction();
		
		$conditions = [];
		foreach ($this->conditions as $groupedObjectTypes) {
			$conditions = array_merge($conditions, $groupedObjectTypes);
		}
		
		ConditionHandler::getInstance()->createConditions($returnValues['returnValues']->removalID, $conditions);
		
		$this->saved();
		
		$this->groupID = 0;
		$this->isDisabled = 0;
		$this->title = '';
		
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->reset();
			}
		}
		
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		if (strlen($this->title) > 255) {
			throw new UserInputException('title', 'tooLong');
		}
		
		if (!isset($this->userGroups[$this->groupID])) {
			throw new UserInputException('groupID', 'noValidSelection');
		}
		
		$hasData = false;
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->validate();
				
				if (!$hasData && $condition->getProcessor()->getData() !== null) {
					$hasData = true;
				}
			}
		}
		
		if (!$hasData) {
			throw new UserInputException('conditions');
		}
	}
}
