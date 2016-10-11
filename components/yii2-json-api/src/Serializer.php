<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace components\jsonapi;

use yii\base\Model;

class Serializer extends \yii\rest\Serializer
{
    /**
     * @var $modelNamespace string the namespace that model classes are in
     */
    public $modelNamespace;

    /**
     * @var $modelMapping array
     */
    public $modelMapping = [];

    /**
     * @var string resources that are related to the primary data
     */
    public $expandParam = 'include';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->modelNamespace === null) {
            $class = get_class(\Yii::$app);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->modelNamespace = substr($class, 0, $pos) . '\\models';
            }
        }
        parent::init();
    }

    /**
     * Serializes a model object.
     * @param Model $model
     * @return array
     */
    public function serializeModel($model)
    {
        $data = [];

        if ($this->request->getIsHead()) {
            return null;
        } else {
            list ($fields, $relationships) = $this->getRequestedFields();
            if (!empty($relationships)) {
                // TODO Implement included. Compound Documents
            }
            $data['data'] = $model->toArray($fields);
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function getRequestedFields()
    {
        $fields = $this->request->get($this->fieldsParam);
        $relationships = $this->request->get($this->expandParam);

        if (!is_array($fields)) {
            $fields = [];
        }
        foreach ($fields as $key => $field) {
            $fields[$key] = preg_split('/\s*,\s*/', $fields, -1, PREG_SPLIT_NO_EMPTY);
        }
        $relationships = preg_split('/\s*,\s*/', $relationships, -1, PREG_SPLIT_NO_EMPTY);
        return [
            $fields,
            $relationships
        ];
    }
}
