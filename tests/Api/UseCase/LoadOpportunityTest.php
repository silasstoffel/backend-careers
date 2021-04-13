<?php

use Laravel\Lumen\Testing\DatabaseMigrations;


class LoadOpportunityTest extends TestCase
{

    use DatabaseMigrations;

    private array $recruiter;
    private $opportunity = [
        "title" => "Senior PHP Developer",
        "description" => "Senior PHP Developer",
        "status" => "opened",
        "salary"=> 10000.00,
        "address" => "Vitoria - ES"
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_login    = 'r1@gmail.com';
        $this->_password = '123456';

        $this->recruiter = [
            "email" => $this->_login,
            "name" => "Recruiter1",
            "password" => $this->_password,
            "company_id" => "company1"
        ];

        $this->createCompany();

        $requestHelper = new ApiCommonRequestHelpers();
        $requestHelper->createRecruiterAccount($this, $this->recruiter);
    }

    public function OtestShouldLoadOpportunity()
    {
        $response = $this->createOpportunity($this->opportunity);
        $response->seeStatusCode(201);

        $body = $response->response->getOriginalContent();

        $response = $this->loadOpportunityById($body['id']);
        $response->seeStatusCode(200);
        $response->seeJsonContains([
            "id" => $body['id'],
            "title" => "Senior PHP Developer",
            "description" => "Senior PHP Developer",
            "status" => "opened",
            "salary"=> 10000.00,
            "address" => "Vitoria - ES",
            'company_id' => 'company1',
        ]);
    }

    public function testShouldNotLoadOpportunity()
    {
        $response = $this->createOpportunity($this->opportunity);
        $response->seeStatusCode(201);
        $response = $this->loadOpportunityById('id-not-exists');
        $response->seeStatusCode(400);
    }

    private function loadOpportunityById(string $id): LoadOpportunityTest
    {
        $headers  = $this->createToken();
        return $this->json('GET', '/v1/opportunities/'.$id, $headers, $headers);
    }

    private function createCompany($index = '1')
    {
        $dbHelper = new TestDbHelpers();
        $dbHelper->createCompany([
            'id' => 'company' . $index,
            'name' => 'Company' . $index
        ]);
    }

    private function createOpportunity($data){
        $headers  = $this->createToken();
        return $this->json('POST', '/v1/opportunities', $data, $headers);
    }


}
