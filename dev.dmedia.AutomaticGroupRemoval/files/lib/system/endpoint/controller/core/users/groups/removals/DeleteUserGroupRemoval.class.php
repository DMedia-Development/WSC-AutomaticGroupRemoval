<?php

namespace wcf\system\endpoint\controller\core\users\groups\removals;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\data\user\group\removal\UserGroupRemovalAction;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * Deletes the automatic user group removal with the given ID.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
#[DeleteRequest('/core/users/groups/removals/{id:\d+}')]
final class DeleteUserGroupRemoval implements IController
{
    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        WCF::getSession()->checkPermissions(['admin.user.canManageGroupAssignment']);

        $removal = Helper::fetchObjectFromRequestParameter($variables['id'], UserGroupRemoval::class);

        (new UserGroupRemovalAction([$removal], 'delete'))->executeAction();

        return new JsonResponse([]);
    }
}
