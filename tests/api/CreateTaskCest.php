<?php


class CreateTaskCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    protected $endUri = 'tasks';
    protected $errorData = [
        "errors" => [
            [
                "status" => "integer",
                "title" => "string",
                "Detail" => "string",
            ]
        ]
    ];

    // tests
    public function createTask(ApiTester $I)
    {
        $I->wantTo('create a task via API');
        $data = [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "begin" => "2016-10-05 11:00",
                    "end" => "2016-10-05 12:00"
                ]
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST("$this->endUri", $data);
        $I->seeResponseCodeIs(201);
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

    public function createTaskWithWrongTime(ApiTester $I)
    {
        $I->wantTo('create a task with wrong period via API');
        $data = [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "begin" => "2016-10-07 13:00",
                    "end" => "2016-10-07 12:00"
                ]
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST("$this->endUri", $data);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }

    public function createTaskWithDuplicateTime(ApiTester $I)
    {
        $I->wantTo('create a task with duplicate time via API');
        $data = [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "begin" => "2016-10-04 11:00",
                    "end" => "2016-10-04 12:00"
                ]
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST("$this->endUri", $data);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }

    public function createTaskWithIncorrectData(ApiTester $I)
    {
        $I->wantTo('create a task with incorrect data via API');
        $data = [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "begin" => "dasda",
                    "end" => "asdasd"
                ]
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST("$this->endUri", $data);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }

    public function createTaskWithNoData(ApiTester $I)
    {
        $I->wantTo('create a task with no data via API');
        $data = [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "end" => "asdasd"
                ]
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST("$this->endUri", $data);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }
}
