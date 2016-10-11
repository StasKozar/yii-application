<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace components\jsonapi;

interface ResourceIdentifierInterface
{
    public function getId();

    public function getType();
}
