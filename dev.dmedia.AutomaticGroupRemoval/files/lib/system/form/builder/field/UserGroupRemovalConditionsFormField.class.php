<?php

namespace wcf\system\form\builder\field;

use wcf\data\IStorableObject;
use wcf\data\object\type\ObjectType;
use wcf\data\user\group\removal\UserGroupRemoval;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\IFormField;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Form field that renders, validates and persists the user conditions of an
 * automatic user group removal. Unfortunately, still no native support.
 *
 * The conditions are provided by the legacy `ICondition` API and are therefore
 * read from and written to the request by this field itself instead of relying
 * on the prefixed field value handling of the form builder.
 *
 * @author Moritz Dahlke (DMedia)
 * @copyright 2020-2026 DMedia
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class UserGroupRemovalConditionsFormField extends AbstractFormField
{
    use TDefaultIdFormField;

    /**
     * list of grouped user group removal condition object types
     * @var ObjectType[][]
     */
    private array $groupedObjectTypes = [];

    public function __construct()
    {
        $this->label('wcf.acp.group.removal.conditions');
        $this->description('wcf.acp.group.removal.conditions.description');
        $this->addValidator(new FormFieldValidator(
            'conditions',
            static function (IFormField $field): void {
                \assert($field instanceof self);

                if (!$field->hasConditionData()) {
                    $field->addValidationError(new FormFieldValidationError(
                        'noConditions',
                        'wcf.acp.group.removal.error.noConditions'
                    ));

                    return;
                }

                foreach ($field->groupedObjectTypes as $groupedObjectTypes) {
                    foreach ($groupedObjectTypes as $conditionObjectType) {
                        try {
                            $conditionObjectType->getProcessor()->validate();
                        } catch (UserInputException $e) {
                            $field->addValidationError(new FormFieldValidationError(
                                $e->getType(),
                                null,
                                $e->getVariables()
                            ));
                        }
                    }
                }
            }
        ));
    }

    /**
     * @inheritDoc
     */
    protected static function getDefaultId()
    {
        return 'conditions';
    }

    /**
     * Sets the list of grouped condition object types and returns this field.
     *
     * @param ObjectType[][] $groupedObjectTypes
     */
    public function groupedObjectTypes(array $groupedObjectTypes): self
    {
        $this->groupedObjectTypes = $groupedObjectTypes;

        return $this;
    }

    /**
     * Returns the list of grouped condition object types.
     *
     * @return ObjectType[][]
     */
    public function getGroupedObjectTypes(): array
    {
        return $this->groupedObjectTypes;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function hasSaveValue()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readValue()
    {
        foreach ($this->groupedObjectTypes as $groupedObjectTypes) {
            foreach ($groupedObjectTypes as $conditionObjectType) {
                $conditionObjectType->getProcessor()->readFormParameters();
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function updatedObject(array $data, IStorableObject $object, $loadValues = true)
    {
        if ($loadValues && $object instanceof UserGroupRemoval) {
            foreach ($object->getConditions() as $condition) {
                $conditionGroup = $condition->getObjectType()->conditiongroup;
                if (isset($this->groupedObjectTypes[$conditionGroup][$condition->objectTypeID])) {
                    $this->groupedObjectTypes[$conditionGroup][$condition->objectTypeID]
                        ->getProcessor()
                        ->setData($condition);
                }
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getHtml()
    {
        return WCF::getTPL()->render('wcf', 'userGroupRemovalConditions', ['field' => $this]);
    }

    /**
     * Resets all condition processors.
     */
    public function reset(): void
    {
        foreach ($this->groupedObjectTypes as $groupedObjectTypes) {
            foreach ($groupedObjectTypes as $conditionObjectType) {
                $conditionObjectType->getProcessor()->reset();
            }
        }
    }

    /**
     * Flattens the grouped object types into the list expected by the
     * condition handler.
     *
     * @return ObjectType[]
     */
    public function getConditionObjectTypes(): array
    {
        $conditionObjectTypes = [];
        foreach ($this->groupedObjectTypes as $groupedObjectTypes) {
            $conditionObjectTypes = \array_merge($conditionObjectTypes, $groupedObjectTypes);
        }

        return $conditionObjectTypes;
    }

    /**
     * Returns an error message if no condition has been filled out.
     */
    public function getNoConditionsErrorMessage(): ?string
    {
        foreach ($this->getValidationErrors() as $validationError) {
            if ($validationError->getType() === 'noConditions') {
                return $validationError->getMessage();
            }
        }

        return null;
    }

    /**
     * Returns `true` if at least one condition has been filled out.
     */
    private function hasConditionData(): bool
    {
        foreach ($this->groupedObjectTypes as $groupedObjectTypes) {
            foreach ($groupedObjectTypes as $conditionObjectType) {
                if ($conditionObjectType->getProcessor()->getData() !== null) {
                    return true;
                }
            }
        }

        return false;
    }
}
