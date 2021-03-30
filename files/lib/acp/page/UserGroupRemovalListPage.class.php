<?php
namespace wcf\acp\page;
use wcf\data\user\group\removal\UserGroupRemovalList;
use wcf\page\MultipleLinkPage;

class UserGroupRemovalListPage extends MultipleLinkPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.group.removal';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.user.canManageGroupAssignment'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = UserGroupRemovalList::class;
}
