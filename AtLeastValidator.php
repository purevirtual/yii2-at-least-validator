<?php

namespace slinstj\yii2-choose-validator;

use yii\base\InvalidConfigException;
use yii\validators\Validator;
/**
 * Checks if one or more in a list of attributes are filled.
 *
 * In the following example, the `attr1` and `attr2` attributes will
 * be verified. If none of them are filled `attr1` will receive an error:
 *
 * ~~~[php]
 *      // in rules()
 *      return [
 *          ['attr1', AtLeastValidator::className(), 'in' => ['attr1', 'attr2']],
 *      ];
 * ~~~
 *
 * In the following example, the `attr1`, `attr2` and `attr3` attributes will
 * be verified. If at least 2 (`min`) of them are not filled, `attr1` will
 * receive an error:
 *
 * ~~~[php]
 *      // in rules()
 *      return [
 *          ['attr1', AtLeastValidator::className(), 'min' => 2, 'in' => ['attr1', 'attr2', 'attr3']],
 *      ];
 * ~~~
 *
 * @author Sidney Lins <slinstj@gmail.com>
 */
class AtLeastValidator extends Validator
{
    /**
     * @var integer the minimun required quantity of attributes that must to be filled.
     * Defaults to 1.
     */
    public $min = 1;

    /**
     * @var string|array the list of attributes that should receive the error message.
     * Defaults to all attributes being validated.
     */
    public $in;

    /**
     * @var boolean whether this validation rule should be skipped if the attribute value
     * is null or an empty string.
     */
    public $skipOnEmpty = false;

    public $skipOnError = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->in === null) {
            throw new InvalidConfigException('The `in` parameter is required.');
        }
        if ($this->message === null) {
            $this->message = 'You must fill at least {min} of the attributes {attributes}.';
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $attributes = $this->in ? (array) $this->in : (array) $attribute;
        $chosen = 0;

        foreach ($attributes as $attributeName) {
            $value = $model->$attributeName;
            $attributesListLabels[] = '"' . $model->generateAttributeLabel($attributeName) . '"';
            $chosen += !empty($value) ? 1 : 0;
        }

        if (!$chosen) {
            $attributesList = implode(', ', $attributesListLabels);
            $message = strtr($this->message, [
                '{min}' => $this->min,
                '{attributes}' => $attributesList,
            ]);
            $model->addError($attribute, $message);
        }
    }
}
