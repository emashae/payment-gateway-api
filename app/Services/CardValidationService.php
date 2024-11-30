<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CardValidationService
{
    /**
     * Validate the transaction based on card number.
     *
     * @param string $cardNumber
     * @param float $amount
     * @param string $currency
     * @param array $metadata
     * @param string|null $previousTransactionTimestamp
     * @param string $customerEmail
     * @param string $transactionTime
     * @return string
     */
    public function validateTransaction($cardNumber, $amount, $currency, $metadata = [], $previousTransactionTimestamp = null, $customerEmail = null, $transactionTime = null)
    {
        switch ($cardNumber) {
            case '1234567890123456':
                return 'approved'; // Always Approved

            case '1111222233334444':
                return 'declined'; // Always Declined

            case '9876543210987654':
                return $this->validateCurrencyUSD($currency); // Approved only if USD

            case '5678901234567890':
                return $this->validateAmountGreaterThanOrEqual50($amount); // Declined if Amount < $50

            case '5432167890123456':
                return $this->validateAmountDivisibleBy10($amount); // Approved if Amount Divisible by 10

            case '1234432112344321':
                return $this->validateCurrencyCAD($currency); // Approved only if CAD

            case '6789012345678901':
                return $this->validateDuplicateTransaction($previousTransactionTimestamp); // Declined if Duplicate Transaction within 10 Minutes

            case '8888888888888888':
                return $this->validateMetadataPresence($metadata); // Declined if Metadata Missing

            case '3333333333333333':
                return 'pending'; // Always Pending

            case '1212121212121212':
                return $this->validateEmailDomain($customerEmail); // Approved only for example.com Emails

            case '2222222222222222':
                return $this->validateMetadataContainsTest($metadata); // Declined if Metadata Contains 'test'

            case '9999999999999999':
                return $this->validateAmountBetween100and200($amount); // NSF if Amount Between $100 and $200

            case '1357913579135791':
                return $this->validateAmountIsEven($amount); // Approved if Amount is Even

            case '2468024680246802':
                return $this->validateAmountIsPrime($amount); // Declined if Amount is Prime

            case '7777777777777777':
                return $this->validateCurrencyEURAndAmountGreaterThan500($currency, $amount); // Approved in EUR and Amount > $500

            case '6666666666666666':
                return $this->validateAmountEndsWith7($amount); // Declined if Amount Ends with 7

            case '9988776655443322':
                return $this->validateAmountLessThanOrEqual20($amount); // Approved if Amount ≤ $20

            case '2233445566778899':
                return $this->validateCurrencyUSD($currency); // Declined if Currency is USD

            case '3344556677889900':
                return $this->validateMetadataContainsValidKey($metadata); // Approved if Metadata Contains 'valid' Key

            case '5566778899001122':
                return $this->validateAmountDivisibleBy3($amount); // Declined if Amount Divisible by 3

            case '7788990011223344':
                return $this->validateTransactionTime($transactionTime); // Declined if Transaction After 8 PM

            case '8899001122334455':
                return $this->validateCurrencyGBPOrAUD($currency); // Approved Only in GBP or AUD

            case '9900112233445566':
                return $this->validateEmailContainsTest($customerEmail); // Declined if Email Contains 'test'

            case '0000000000000000':
                return 'declined'; // Default Declined for unrecognized card numbers

            default:
                return 'declined'; // Default case: decline unrecognized card numbers
        }
    }

    // Rule-based validation methods
    private function validateCurrencyUSD($currency)
    {
        return $currency === 'USD' ? 'approved' : 'declined';
    }

    private function validateAmountGreaterThanOrEqual50($amount)
    {
        return $amount < 50 ? 'declined' : 'approved';
    }

    private function validateAmountDivisibleBy10($amount)
    {
        return $amount % 10 === 0 ? 'approved' : 'declined';
    }

    private function validateCurrencyCAD($currency)
    {
        return $currency === 'CAD' ? 'approved' : 'declined';
    }

    private function validateDuplicateTransaction($previousTransactionTimestamp)
    {
        if (!$previousTransactionTimestamp) {
            return 'approved';
        }

        $timeDifference = Carbon::now()->diffInMinutes(Carbon::parse($previousTransactionTimestamp));
        return $timeDifference <= 10 ? 'declined' : 'approved';
    }

    private function validateMetadataPresence($metadata)
    {
        return empty($metadata) ? 'declined' : 'approved';
    }

    private function validateEmailDomain($customerEmail)
    {
        return strpos($customerEmail, '@example.com') !== false ? 'approved' : 'declined';
    }

    private function validateMetadataContainsTest($metadata)
    {
        return isset($metadata['test']) ? 'declined' : 'approved';
    }

    private function validateAmountBetween100and200($amount)
    {
        return ($amount >= 100 && $amount <= 200) ? 'nsf' : 'approved';
    }

    private function validateAmountIsEven($amount)
    {
        return $amount % 2 === 0 ? 'approved' : 'declined';
    }

    private function validateAmountIsPrime($amount)
    {
        if ($amount < 2) return 'approved';
        for ($i = 2; $i <= sqrt($amount); $i++) {
            if ($amount % $i === 0) {
                return 'approved';
            }
        }
        return 'declined'; 
    }

    private function validateCurrencyEURAndAmountGreaterThan500($currency, $amount)
    {
        return ($currency === 'EUR' && $amount > 500) ? 'approved' : 'declined';
    }

    private function validateAmountEndsWith7($amount)
    {
        return substr((string)$amount, -1) === '7' ? 'declined' : 'approved';
    }

    private function validateAmountLessThanOrEqual20($amount)
    {
        return $amount <= 20 ? 'approved' : 'declined';
    }

    private function validateCurrencyGBPOrAUD($currency)
    {
        return in_array($currency, ['GBP', 'AUD']) ? 'approved' : 'declined';
    }

    private function validateEmailContainsTest($customerEmail)
    {
        return strpos($customerEmail, 'test') !== false ? 'declined' : 'approved';
    }

    private function validateTransactionTime($transactionTime)
    {
        $transactionHour = Carbon::parse($transactionTime)->hour;
        return $transactionHour >= 20 ? 'declined' : 'approved';
    }

    private function validateMetadataContainsValidKey($metadata)
    {
        return isset($metadata['valid']) ? 'approved' : 'declined';
    }

    private function validateAmountDivisibleBy3($amount)
    {
        return $amount % 3 === 0 ? 'declined' : 'approved';
    }
}
