<?php
namespace wcf\data\user\group\removal;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\ConditionCacheBuilder;
use wcf\system\cache\builder\UserGroupRemovalCacheBuilder;

class UserGroupRemovalEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = UserGroupRemoval::class;
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		UserGroupRemovalCacheBuilder::getInstance()->reset();
		ConditionCacheBuilder::getInstance()->reset([
			'definitionID' => ObjectTypeCache::getInstance()->getDefinitionByName('dev.dmedia.AutomaticGroupRemoval.condition.userGroupRemoval')->definitionID
		]);
	}
}
