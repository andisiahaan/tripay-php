<?php

use PHPUnit\Framework\TestCase;
use AndiSiahaan\Tripay\Utils\Signer;

class ClientTest extends TestCase
{
    public function testSignerProducesDeterministicSignature()
    {
        $payload = ['b' => 'two', 'a' => 'one'];
        $sig = Signer::sign($payload, 'secret');
        $this->assertIsString($sig);
        $this->assertEquals(hash_hmac('sha256', 'a=one&b=two', 'secret'), $sig);
    }

    public function testPaymentInstructionFactoryAndValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $pi = $client->paymentInstruction();
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\PaymentInstruction::class, $pi);

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $pi->get([]); // missing 'code' should throw
    }

    public function testPaymentChannelsFactory()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $pc = $client->paymentChannels();
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\PaymentChannels::class, $pc);
    }

    public function testFeeCalculatorFactoryAndValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $fc = $client->feeCalculator();
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\FeeCalculator::class, $fc);

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $fc->get(['code' => 'QRIS']); // missing amount should throw
    }

    public function testTransactionsFactory()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $tx = $client->transactions();
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\Transactions::class, $tx);
    }

    public function testTransactionsDetailValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $tx = $client->transactions();

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $tx->detail([]); // missing reference should throw
    }

    public function testTransactionsStatusValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $tx = $client->transactions();

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $tx->status([]); // missing reference should throw
    }

    public function testTransactionFactoryAndValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\Transaction::class, $client->transaction());

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        // Missing required fields should throw
        $client->transaction()->create([]);
    }

    public function testOpenPaymentFactoryAndValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $this->assertInstanceOf(\AndiSiahaan\Tripay\Endpoints\OpenPayment::class, $client->openPayment());

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $client->openPayment()->create([]); // missing required fields
    }

    public function testOpenPaymentDetailValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $op = $client->openPayment();

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $op->detail(''); // empty uuid should throw
    }

    public function testOpenPaymentTransactionsValidation()
    {
        $client = new \AndiSiahaan\Tripay\Client('key', 'secret', false);
        $op = $client->openPayment();

        $this->expectException(\AndiSiahaan\Tripay\Exceptions\TripayException::class);
        $op->transactions(''); // empty uuid should throw
    }
}
