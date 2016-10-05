<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace components\jsonapi\tests\data;

use components\jsonapi\ResourceIdentifierInterface;
use components\jsonapi\ResourceTrait;
use yii\base\Model;

class ResourceModel extends Model implements ResourceIdentifierInterface
{
    use ResourceTrait;

    public $testAttribute = 'testAttribute';

    public function getTestRelation()
    {
        return new self;
    }

    public function extraFields()
    {
        return ['testRelation'];
    }
}
