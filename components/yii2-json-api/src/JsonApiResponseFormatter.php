<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace components\jsonapi;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

class JsonApiResponseFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * @var integer the encoding options passed to [[Json::encode()]]. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>.
     * Default is `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     */
    public $encodeOptions = 320;
    /**
     * @var bool whether to format the output in a readable "pretty" format. This can be useful for debugging purpose.
     * If this is true, `JSON_PRETTY_PRINT` will be added to [[encodeOptions]].
     * Defaults to `false`.
     */
    public $prettyPrint = false;

    /**
     * Formats response data in JSON format.
     * @link http://jsonapi.org/format/upcoming/#document-structure
     * @param Response $response
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/vnd.api+json');
        if ($response->data !== null) {
            $options = $this->encodeOptions;
            if ($this->prettyPrint) {
                $options |= JSON_PRETTY_PRINT;
            }
            $topMember = 'data';
            $apiDocument = [];
            if ($response->isClientError || $response->isServerError) {
                if (ArrayHelper::isAssociative($response->data)) {
                    $response->data = [$response->data];
                }
                $topMember = 'errors';
            }
            if ($meta = ArrayHelper::getValue($response->data, 'meta')) {
                unset($response->data['meta']);
                $apiDocument['meta'] = $meta;
            }
            if (ArrayHelper::keyExists($topMember, $response->data)) {
                $apiDocument = $response->data;
            }else{
                $apiDocument[$topMember] = $response->data;
            }

            $response->content = Json::encode($apiDocument, $options);
        }
    }
}
