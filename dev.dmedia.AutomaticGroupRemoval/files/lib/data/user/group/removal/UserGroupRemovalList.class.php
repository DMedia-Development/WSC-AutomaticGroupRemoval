<?php

namespace wcf\data\user\group\removal;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2024 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  UserGroupRemoval     current()
 * @method  UserGroupRemoval[]       getObjects()
 * @method  UserGroupRemoval|null    getSingleObject()
 * @method  UserGroupRemoval|null    search($objectID)
 * @property    UserGroupRemoval[] $objects
 */
class UserGroupRemovalList extends DatabaseObjectList
{
}
