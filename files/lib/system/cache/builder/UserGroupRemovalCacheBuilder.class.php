<?php
namespace wcf\system\cache\builder;
use wcf\data\user\group\removal\UserGroupRemovalList;

class UserGroupRemovalCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$removalList = new UserGroupRemovalList();
		$removalList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$removalList->readObjects();
		
		return $removalList->getObjects();
	}
}
