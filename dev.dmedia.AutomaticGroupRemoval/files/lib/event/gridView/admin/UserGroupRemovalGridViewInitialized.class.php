<?php

namespace wcf\event\gridView\admin;

use wcf\event\IPsr14Event;
use wcf\system\gridView\admin\UserGroupRemovalGridView;

/**
 * Indicates that the user group removal grid view has been initialized.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class UserGroupRemovalGridViewInitialized implements IPsr14Event
{
    public function __construct(public readonly UserGroupRemovalGridView $gridView) {}
}
