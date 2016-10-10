<?php


class GetPeriodCest
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
    public function getPeriod(ApiTester $I)
    {
        $I->wantTo('get period via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri?filter[begin]=2016-09-26&filter[end]=2016-10-02");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "data" => [
                [
                    [
                        "type" => "string",
                        "attributes" => [
                            "begin" => "string",
                            "end" => "string",
                            'periodType' => 'integer',
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function getPeriodWithWrongTime(ApiTester $I)
    {
        $I->wantTo('get period with wrong data via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri?filter[begin]=2016-09-26&filter[end]=2015-10-02");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }

    public function getPeriodWithIncorrectData(ApiTester $I)
    {
        $I->wantTo('get period with incorrect data via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri?filter[begin]=2016-09-26&filter[end]=2015-10-02");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }

    public function getPeriodWithNoData(ApiTester $I)
    {
        $I->wantTo('get period with no data via API');
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET("$this->endUri?filter[begin]=2016-09-26");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->errorData);
    }
}
