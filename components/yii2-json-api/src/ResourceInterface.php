<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace components\jsonapi;

interface ResourceInterface extends ResourceIdentifierInterface
{
    public function getResourceAttributes(array $fields = []);

    public function getResourceRelationships();

    public function getLinks();

    public function getMeta();

}
