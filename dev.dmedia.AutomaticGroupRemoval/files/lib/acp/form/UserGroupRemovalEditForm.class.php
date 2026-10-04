<?php

namespace wcf\acp\form;

use wcf\acp\page\UserGroupRemovalListPage;
use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\system\exception\IllegalLinkException;
use wcf\system\interaction\admin\UserGroupRemovalInteractions;
use wcf\system\interaction\StandaloneInteractionContextMenuComponent;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the form to edit an existing automatic user group removal.
 *
 * @author Moritz Dahlke (DMedia)
 * @author Original Author: Matthias Schmidt
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalEditForm extends UserGroupRemovalAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.removal';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * edited automatic user group removal
     */
    public UserGroupRemoval $removal;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        $removalID = 0;
        if (isset($_REQUEST['id'])) {
            $removalID = \intval($_REQUEST['id']);
        }

        $this->formObject = new UserGroupRemoval($removalID);
        $this->removal = $this->formObject;

        if (!$this->removal->removalID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'removal' => $this->removal,
            'interactionContextMenu' => StandaloneInteractionContextMenuComponent::forContentHeaderButton(
                new UserGroupRemovalInteractions(),
                $this->removal,
                LinkHandler::getInstance()->getControllerLink(UserGroupRemovalListPage::class)
            ),
        ]);
    }
}
