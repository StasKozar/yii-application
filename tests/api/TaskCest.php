<?php


class TaskCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    protected $endUri = 'tasks';

    public function getAllTasks(ApiTester $I)
    {
        $I->wantTo('get all tasks via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "data" => [
                [
                    "type" => "string",
                    "attributes" => [
                        "begin" => "string",
                        "end" => "string"
                    ]
                ]
            ]
        ]);
    }

    public function getSingleTask(ApiTester $I)
    {
        $I->wantTo('get single task via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri/37");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "data" => [
                "type" => "string",
                "attributes" => [
                    "begin" => "string",
                    "end" => "string"
                ]
            ]
        ]);
    }
}
