<?php

namespace wcf\event\interaction\admin;

use wcf\event\IPsr14Event;
use wcf\system\interaction\admin\UserGroupRemovalInteractions;

/**
 * Indicates that the provider for user group removal interactions is collecting interactions.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class UserGroupRemovalInteractionCollecting implements IPsr14Event
{
    public function __construct(public readonly UserGroupRemovalInteractions $provider) {}
}
