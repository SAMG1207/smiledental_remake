<?php
namespace Tests\TestModels;
use PHPUnit\Framework\TestCase;
use App\Models\ClientModel;
use App\Database\Database;
use Firebase\JWT\JWT;

class TestClient extends TestCase
{
    protected $clientModel;

    protected function setUp(): void{
        $this->clientModel = $this->createMock(ClientModel::class);
    }

    public function testlogin(){
        $email = 'sergio@gmail.com';
        $password = '123456';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $jwt = $this->clientModel->loginClient($email, $password);
        $this->assertIsString($jwt);
    }
}
