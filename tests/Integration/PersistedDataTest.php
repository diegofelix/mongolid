<?php
namespace Mongolid\Tests\Integration;

use MongoDB\BSON\ObjectId;
use Mongolid\Tests\Integration\Stubs\User;

class PersistedDataTest extends IntegrationTestCase
{
    /**
     * @var ObjectId
     */
    private $_id;

    protected function setUp()
    {
        parent::setUp();
        $this->_id = new ObjectId('5bcb310783a7fcdf1bf1a672');
    }

    public function testSaveInsertingData()
    {
        // Set
        $user = $this->getUser();

        $expected = [
            '_id' => (string) $this->_id,
            'name' => 'John Doe',
            'age' => 25,
            'height' => 1.80,
            'preferences' => [
                'email' => 'never',
            ],
            'friends' => [],
            'skills' => [
                'PHP' => ['percentage' => '100%', 'version' => '7.0'],
                'JavaScript' => ['percentage' => '80%', 'version' => 'ES6'],
                'CSS' => ['percentage' => '45%', 'version' => 'CSS3'],
            ],
            'photos' => ['profile' => '/user-photo', 'icon' => '/user-icon'],
        ];

        // Actions
        $saveResult = $user->save();
        $result = (array) $user->collection()->findOne(['_id' => $this->_id]);
        $result['_id'] = (string) ($result['_id'] ?? '');

        // Assertions
        $this->assertTrue($saveResult);
        $this->assertSame($expected, $result);
    }

    public function testSaveUpdatingData()
    {
        // Set
        $user = $this->getUser(true);

        $user->name = 'Jane Doe';
        unset($user->age);
        $user->height = null;
        $user->email = 'jane@doe.com';
        $user->preferences = [];
        $user->friends = ['Mary'];
        $user->address = '123 Blue Street';
        $user->skills->HTML = ['percentage' => '89%', 'version' => 'HTML5'];
        $user->skills->PHP['version'] = '7.1';

        $expected = [
            '_id' => (string) $user->_id,
            'name' => 'Jane Doe',
            'preferences' => [],
            'friends' => ['Mary'],
            'address' => '123 Blue Street',
            'skills' => [
                'PHP' => ['percentage' => '100%', 'version' => '7.1'],
                'JavaScript' => ['percentage' => '80%', 'version' => 'ES6'],
                'CSS' => ['percentage' => '45%', 'version' => 'CSS3'],
                'HTML' => ['percentage' => '89%', 'version' => 'HTML5'],
            ],
            'photos' => ['profile' => '/user-photo', 'icon' => '/user-icon'],
            'email' => 'jane@doe.com',
        ];

        // Actions
        $updateResult = $user->save();
        $result = (array) $user->collection()->findOne(['_id' => $user->_id]);
        $result['_id'] = (string) ($result['_id'] ?? '');

        // Assertions
        $this->assertTrue($updateResult);
        $this->assertSame($expected, $result);
    }

    public function testUpdateData()
    {
        // Set
        $user = $this->getUser(true);

        $user->name = 'Jane Doe';
        unset($user->age);
        $user->height = null;
        $user->email = 'jane@doe.com';
        $user->preferences = [];
        $user->friends = ['Mary'];
        $user->address = '123 Blue Street';
        $user->skills->HTML = ['percentage' => '89%', 'version' => 'HTML5'];
        $user->skills->PHP['version'] = '7.1';

        $expected = [
            '_id' => (string) $user->_id,
            'name' => 'Jane Doe',
            'preferences' => [],
            'friends' => ['Mary'],
            'skills' => [
                'PHP' => ['percentage' => '100%', 'version' => '7.1'],
                'JavaScript' => ['percentage' => '80%', 'version' => 'ES6'],
                'CSS' => ['percentage' => '45%', 'version' => 'CSS3'],
                'HTML' => ['percentage' => '89%', 'version' => 'HTML5'],
            ],
            'photos' => ['profile' => '/user-photo', 'icon' => '/user-icon'],
            'address' => '123 Blue Street',
            'email' => 'jane@doe.com',
        ];

        // Actions
        $updateResult = $user->update();
        $result = (array) $user->collection()->findOne(['_id' => $user->_id]);
        $result['_id'] = (string) ($result['_id'] ?? '');

        // Assertions
        $this->assertTrue($updateResult);
        $this->assertSame($expected, $result);
    }

    private function getUser(bool $save = false): User
    {
        $user = new User();
        $user->_id = $this->_id;
        $user->name = 'John Doe';
        $user->age = 25;
        $user->height = 1.80;
        $user->preferences = [
            'email' => 'never',
        ];
        $user->friends = [];
        $user->address = null;
        $user->skills = (object) [
            'PHP' => ['percentage' => '100%', 'version' => '7.0'],
            'JavaScript' => ['percentage' => '80%', 'version' => 'ES6'],
            'CSS' => ['percentage' => '45%', 'version' => 'CSS3'],
        ];

        // dinamically set array
        $user->photos['profile'] = '/user-photo';
        $user->photos['icon'] = '/user-icon';

        // access unknown field and don't find it saved later.
        $user->unknown;

        if ($save) {
            $this->assertTrue($user->save(), 'Failed to save user!');
        }

        return $user;
    }
}