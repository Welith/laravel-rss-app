<?php

namespace Tests\Feature;

use App\Constants\RequestConstants;
use Tests\TestCase;

class GoServiceIntegrationTest extends TestCase
{
    /** @test */
    public function fetch_success()
    {
        $data = [
            'urls' => ['https://www.theboltonnews.co.uk/news/rss/'],
            'username' => 'emerchantpay',
            'password' => 'password'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['fetched_go'], $response['message']);
    }

    /** @test */
    public function fetch_fail_invalid_username()
    {
        $data = [
            'urls' => ['https://www.theboltonnews.co.uk/news/rss/'],
            'username' => 'test',
            'password' => 'password'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['unauthorized'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['unauthorized'], $response['message']);
    }

    /** @test */
    public function fetch_fail_missing_username()
    {
        $data = [
            'urls' => ['https://www.theboltonnews.co.uk/news/rss/'],
            'username' => null,
            'password' => 'password'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The username field is required.", $response['message']['username'][0]);
    }

    /** @test */
    public function fetch_fail_invalid_password()
    {
        $data = [
            'urls' => ['https://www.theboltonnews.co.uk/news/rss/'],
            'username' => 'emerchantpay',
            'password' => 'test'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['unauthorized'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['unauthorized'], $response['message']);
    }

    /** @test */
    public function fetch_fail_missing_password()
    {
        $data = [
            'urls' => ['https://www.theboltonnews.co.uk/news/rss/'],
            'username' => 'emerchantpay',
            'password' => null
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The password field is required.", $response['message']['password'][0]);
    }

    /** @test */
    public function fetch_fail_missing_urls()
    {
        $data = [
            'urls' => null,
            'username' => 'emerchantpay',
            'password' => 'test'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The urls field is required.", $response['message']['urls'][0]);
    }

    /** @test */
    public function fetch_fail_invalid_url_array()
    {
        $data = [
            'urls' => "test",
            'username' => 'emerchantpay',
            'password' => 'test'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The urls must be an array.", $response['message']['urls'][0]);
    }

    /** @test */
    public function fetch_fail_invalid_urls()
    {
        $data = [
            'urls' => ["test"],
            'username' => 'emerchantpay',
            'password' => 'test'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The urls.0 must be a valid URL.", $response['message']['urls.0'][0]);
    }

    /** @test */
    public function fetch_fail_null_urls()
    {
        $data = [
            'urls' => [null],
            'username' => 'emerchantpay',
            'password' => 'test'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The urls.0 field is required.", $response['message']['urls.0'][0]);
    }

    /** @test */
    public function fetch_fail_no_feeds()
    {
        $data = [
            'urls' => ["http://facebook.com"],
            'username' => 'emerchantpay',
            'password' => 'password'
        ];

        $response = $this->call("POST", route('feeds.go-fetch'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['not_found'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['not_found_go'], $response['message']);
    }
}
