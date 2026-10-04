<?php

namespace wcf\acp\form;

use wcf\data\user\group\removal\UserGroupRemovalAction;
use wcf\data\user\group\UserGroup;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\IsDisabledFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\UserGroupRemovalConditionsFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\IFormField;
use wcf\system\user\group\removal\UserGroupRemovalHandler;

/**
 * Shows the form to create a new automatic user group removal.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserGroupRemovalAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.removal.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageGroupAssignment'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = UserGroupRemovalAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = UserGroupRemovalEditForm::class;

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create()
                        ->label('wcf.global.name')
                        ->required()
                        ->autoFocus()
                        ->addValidator(new FormFieldValidator(
                            'titleLength',
                            static function (IFormField $field): void {
                                if (\mb_strlen((string)$field->getValue()) > 255) {
                                    $field->addValidationError(new FormFieldValidationError(
                                        'tooLong',
                                        'wcf.acp.group.removal.title.error.tooLong'
                                    ));
                                }
                            }
                        )),
                    SelectFormField::create('groupID')
                        ->label('wcf.user.group')
                        ->required()
                        ->ignoreInvalidValues()
                        ->options($this->getUserGroupOptions()),
                    IsDisabledFormField::create()
                        ->label('wcf.acp.group.removal.isDisabled'),
                ]),
            UserGroupRemovalConditionsFormField::create('conditions')
                ->groupedObjectTypes(UserGroupRemovalHandler::getInstance()->getGroupedObjectTypes()),
        ]);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save()
    {
        // grab the condition object types before the form is rebuilt on creation
        $conditionsField = $this->form->getNodeById('conditions');
        \assert($conditionsField instanceof UserGroupRemovalConditionsFormField);
        $conditionObjectTypes = $conditionsField->getConditionObjectTypes();

        parent::save();

        $removal = $this->formObject ?? $this->objectAction->getReturnValues()['returnValues'];

        if ($this->formAction === 'edit') {
            ConditionHandler::getInstance()->updateConditions(
                $removal->removalID,
                $removal->getConditions(),
                $conditionObjectTypes
            );
        } else {
            ConditionHandler::getInstance()->createConditions($removal->removalID, $conditionObjectTypes);

            $rebuiltConditionsField = $this->form->getNodeById('conditions');
            \assert($rebuiltConditionsField instanceof UserGroupRemovalConditionsFormField);
            $rebuiltConditionsField->reset();
        }
    }

    /**
     * Returns the selectable user groups.
     *
     * @return array<int, string>
     */
    protected function getUserGroupOptions(): array
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

            // also exclude groups with ACP access
            if ($userGroup->getGroupOption('admin.general.canUseAcp')) {
                continue;
            }

            $options[$userGroup->groupID] = $userGroup->getTitle();
        }

        return $options;
    }
}
