<?php
namespace AppBundle\Tests\Controller\Api;
use AppBundle\Test\ApiTestCase;
class ProgrammerControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->createUser('weaverryan');
    }
   
   public function testGETProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));
        $response = $this->client->get('/api/programmers/UnitTester');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'nickname',
            'avatarNumber',
            'powerLevel',
            'tagLine'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'UnitTester');
        $this->debugResponse( $response);
    }
   public function testGETProgrammersCollection()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
        ));
        $response = $this->client->get('/api/programmers');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response, 'programmers');
        $this->asserter()->assertResponsePropertyCount($response, 'programmers', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'programmers[1].nickname', 'CowboyCoder');
    }
    
    public function testPUTProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine' => 'foo',
        ));
        $data = array(
            'nickname' => 'CowgirlCoder',
            'avatarNumber' => 2,
            'tagLine' => 'foo',
        );
       
        $response = $this->client->put('/api/programmers/CowboyCoder', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 2);
        // the nickname is immutable on edit
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'CowboyCoder');
    }
    
    public function testDELETEProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));
        $response = $this->client->delete('/api/programmers/UnitTester');
        $this->assertEquals(204, $response->getStatusCode());
    }
    
     public function testPATCHProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine' => 'foo',
            'powerLevel' => 4,
        ));
        $data = array(
            'tagLine' => 'bar',
        );
        $response = $this->client->patch('/api/programmers/CowboyCoder', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 5);
        $this->asserter()->assertResponsePropertyEquals($response, 'tagLine', 'bar');
    }
    
     public function testValidationErrors()
    {
        $data = array(
            'avatarNumber' => 2,
            'tagLine' => 'I\'m from a test!'
        );
        
        // 1) Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data)
        ]);
        
        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors',
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.nickname');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.nickname[0]', 'Please enter a clever nickname');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.avatarNumber');
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type'));
    }
    
    public function testInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "nickname": "JohnnyRobot",
    "avatarNumber" : "2
    "tagLine": "I'm from a test!"
}
EOF;
        $response = $this->client->post('/api/programmers', [
            'body' => $invalidBody
        ]);
        $this->debugResponse($response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'invalid_body_format');
    }
    
    public function test404Exception()
    {
        $response = $this->client->get('/api/programmers/fake');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
    }
    
    
}