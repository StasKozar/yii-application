<?php
///* @var $scenario Codeception\Scenario*/
//$I = new ApiTester($scenario);
//$I->wantTo('update a task via API');
//$data = [
//    "data" => [
//        "type" => "tasks",
//        "attributes" => [
//            "begin" => "2016-10-04 11:00",
//            "end" => "2016-10-04 12:00"
//        ]
//    ]
//];
//
//$I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
//$I->sendPUT('tasks/50', $data);
//$I->seeResponseCodeIs(201);
//$I->seeResponseIsJson();
//$I->seeResponseContains('"data":');
//$I->seeResponseContains('"type": "tasks"');
//$I->seeResponseContains('"attributes":');
//$I->seeResponseContains('"begin": "');
//$I->seeResponseContains('"end": "');