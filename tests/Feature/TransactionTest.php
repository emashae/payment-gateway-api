<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\CardValidationService;

class TransactionTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = new CardValidationService();
    }

    // Test cards that always approve
    public function testAlwaysApprovedCard()
    {
        $result = $this->service->validateTransaction('1234567890123456', 100, 'USD');
        $this->assertEquals('approved', $result);
    }

    // Test cards that always decline
    public function testAlwaysDeclinedCard()
    {
        $result = $this->service->validateTransaction('1111222233334444', 100, 'USD');
        $this->assertEquals('declined', $result);
    }

    // Test cards with USD validation
    public function testValidateCurrencyUSD()
    {
        $result = $this->service->validateTransaction('9876543210987654', 100, 'USD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('9876543210987654', 100, 'CAD');
        $this->assertEquals('declined', $result);
    }

    // Test cards with amount validation greater than or equal to 50
    public function testValidateAmountGreaterThanOrEqual50()
    {
        $result = $this->service->validateTransaction('5678901234567890', 50, 'USD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('5678901234567890', 49, 'USD');
        $this->assertEquals('declined', $result);
    }

    // Test cards with amount divisible by 10
    public function testValidateAmountDivisibleBy10()
    {
        $result = $this->service->validateTransaction('5432167890123456', 100, 'USD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('5432167890123456', 95, 'USD');
        $this->assertEquals('declined', $result);
    }

    // Test cards with CAD validation
    public function testValidateCurrencyCAD()
    {
        $result = $this->service->validateTransaction('1234432112344321', 100, 'CAD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('1234432112344321', 100, 'USD');
        $this->assertEquals('declined', $result);
    }

    
    public function testValidateDuplicateTransaction()
    {
        // Test the duplicate transaction validation
        $result = $this->service->validateDuplicateTransaction('2023-11-28 10:00:00', '9900112233445566', 100, 'USD');
        $this->assertEquals('declined', $result);  
    }

    // Test metadata presence
    public function testValidateMetadataPresence()
    {
        $result = $this->service->validateTransaction('8888888888888888', 100, 'USD', ['key' => 'value']);
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('8888888888888888', 100, 'USD', []);
        $this->assertEquals('declined', $result);
    }

    // Test email domain validation
    public function testValidateEmailDomain()
    {
        $result = $this->service->validateTransaction('1212121212121212', 100, 'USD', [], null, 'user@example.com');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('1212121212121212', 100, 'USD', [], null, 'user@test.com');
        $this->assertEquals('declined', $result);
    }

    // Test metadata contains "test"
    public function testValidateMetadataContainsTest()
    {
        $result = $this->service->validateTransaction('2222222222222222', 100, 'USD', ['test' => 'value']);
        $this->assertEquals('declined', $result);

        $result = $this->service->validateTransaction('2222222222222222', 100, 'USD', ['key' => 'value']);
        $this->assertEquals('approved', $result);
    }

    // Test amount between 100 and 200 (NSF)
    public function testValidateAmountBetween100and200()
    {
        $result = $this->service->validateTransaction('9999999999999999', 150, 'USD');
        $this->assertEquals('nsf', $result);

        $result = $this->service->validateTransaction('9999999999999999', 50, 'USD');
        $this->assertEquals('approved', $result);
    }

    // Test amount is even
    public function testValidateAmountIsEven()
    {
        $result = $this->service->validateTransaction('1357913579135791', 100, 'USD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('1357913579135791', 101, 'USD');
        $this->assertEquals('declined', $result);
    }

    // Test amount is prime
    public function testValidateAmountIsPrime()
    {
        $result = $this->service->validateTransaction('2468024680246802', 3, 'USD');
        $this->assertEquals('declined', $result);

        $result = $this->service->validateTransaction('2468024680246802', 4, 'USD');
        $this->assertEquals('approved', $result);
    }

    // Test currency EUR and amount greater than 500
    public function testValidateCurrencyEURAndAmountGreaterThan500()
    {
        $result = $this->service->validateTransaction('7777777777777777', 600, 'EUR');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('7777777777777777', 400, 'EUR');
        $this->assertEquals('declined', $result);
    }

    // Test amount ends with 7
    public function testValidateAmountEndsWith7()
    {
        $result = $this->service->validateTransaction('6666666666666666', 17, 'USD');
        $this->assertEquals('declined', $result);

        $result = $this->service->validateTransaction('6666666666666666', 18, 'USD');
        $this->assertEquals('approved', $result);
    }

    // Test amount less than or equal to 20
    public function testValidateAmountLessThanOrEqual20()
    {
        $result = $this->service->validateTransaction('9988776655443322', 20, 'USD');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('9988776655443322', 21, 'USD');
        $this->assertEquals('declined', $result);
    }

    // Test currency GBP or AUD
    public function testValidateCurrencyGBPOrAUD()
    {
        $result = $this->service->validateTransaction('8899001122334455', 100, 'GBP');
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('8899001122334455', 100, 'USD');
        $this->assertEquals('declined', $result);
    }

    public function testValidateEmailContainsTest()
    {
        $result = $this->service->validateTransaction('9900112233445566', 100, 'USD', [], 'user@test.com');
        $this->assertEquals('declined', $result);  
    }
    

    // Test transaction time (after 8 PM)
    public function testValidateTransactionTime()
    {
        $result = $this->service->validateTransactionTime('2023-11-29 10:00:00');
        $this->assertEquals('approved', $result); 

        $result = $this->service->validateTransactionTime('2023-11-01 10:00:00');
        $this->assertEquals('declined', $result); 
    }

    // Test metadata contains "valid" key
    public function testValidateMetadataContainsValidKey()
    {
        $result = $this->service->validateTransaction('3344556677889900', 100, 'USD', ['valid' => 'true']);
        $this->assertEquals('approved', $result);

        $result = $this->service->validateTransaction('3344556677889900', 100, 'USD', ['invalid' => 'true']);
        $this->assertEquals('declined', $result);
    }
}



