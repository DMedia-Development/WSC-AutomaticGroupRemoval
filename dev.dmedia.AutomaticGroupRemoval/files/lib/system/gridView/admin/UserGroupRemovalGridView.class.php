<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserGroupRemovalEditForm;
use wcf\data\DatabaseObject;
use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\data\user\group\removal\UserGroupRemovalList;
use wcf\data\user\group\UserGroup;
use wcf\event\gridView\admin\UserGroupRemovalGridViewInitialized;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\UserGroupRemovalInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\view\filter\SelectFilter;
use wcf\system\view\filter\TextFilter;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of automatic user group removals.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridView<UserGroupRemoval, UserGroupRemovalList>
 */
final class UserGroupRemovalGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('removalID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('title')
                ->titleColumn()
                ->label('wcf.global.name')
                ->renderer(new DefaultColumnRenderer())
                ->filter(TextFilter::class)
                ->sortable(),
            GridViewColumn::for('groupID')
                ->label('wcf.acp.group.removal.userGroup')
                ->filter(new SelectFilter(
                    $this->getAvailableUserGroups(),
                    'groupID',
                    'wcf.acp.group.removal.userGroup',
                    labelLanguageItems: false
                ))
                ->sortable()
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserGroupRemoval);

                            return StringUtil::encodeHTML($row->getUserGroup()->getTitle());
                        }
                    }
                ),
        ]);

        $provider = new UserGroupRemovalInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(UserGroupRemovalEditForm::class),
        ]);
        $this->setInteractionProvider($provider);

        $this->addQuickInteraction(
            new ToggleInteraction(
                'enable',
                'core/users/groups/removals/%s/enable',
                'core/users/groups/removals/%s/disable'
            )
        );

        $this->setDefaultSortField('title');
        $this->addRowLink(new GridViewRowLink(UserGroupRemovalEditForm::class));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.user.canManageGroupAssignment');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function createObjectList(): UserGroupRemovalList
    {
        return new UserGroupRemovalList();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getInitializedEvent(): ?UserGroupRemovalGridViewInitialized
    {
        return new UserGroupRemovalGridViewInitialized($this);
    }

    /**
     * Returns the selectable user groups for the user group filter.
     *
     * @return array<int, string>
     */
    private function getAvailableUserGroups(): array
    {
        $userGroups = UserGroup::getSortedGroupsByType([], [
            UserGroup::EVERYONE,
            UserGroup::GUESTS,
            UserGroup::OWNER,
            UserGroup::USERS,
        ]);

        $options = [];
        foreach ($userGroups as $userGroup) {
            if (!$userGroup->isAccessible()) {
                continue;
            }

            $options[$userGroup->groupID] = $userGroup->getTitle();
        }

        return $options;
    }
}
