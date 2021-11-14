<?php

namespace Tests\Feature;

use App\Constants\RequestConstants;
use App\Models\Feed;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $feed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feed = Feed::factory(10)->create(); // create 10 feeds
        $this->feed = Feed::factory()->create(
            [
                "title" => "sameTitle",
                "link" => "sameLink"
            ]
        ); // create 10 feeds

        $this->feed = Feed::factory(1)->create([
            'title' => 'testTitle'
        ]);
        $this->feed = Feed::factory(1)->create([
            'link' => 'testLink'
        ]);

        $this->feed = Feed::factory(1)->create([
            'publish_date' => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 12:12:00")
        ]);
    }

    ##### INDEX #####

    /** @test */
    public function index_returns_all_feeds_as_json()
    {
        $filter = [];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertCount(14, $response['feeds']['data']);
        $this->assertArrayHasKey('id', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('description', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('link', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('source', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('source_url', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('publish_date', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('title', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('created_at', $response['feeds']['data'][0]);
        $this->assertArrayHasKey('updated_at', $response['feeds']['data'][0]);
    }

    /** @test */
    public function index_returns_all_feeds_as_json_filtered_title()
    {
        $filter = [
            "title" => "testTitle"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(1, $response['feeds']['data']); // only one a title testTitle
        $this->assertEquals('testTitle', $response['feeds']['data'][0]['title']);
    }

    /** @test */
    public function index_returns_empty_items_as_json_filtered_title_non_existent()
    {
        $filter = [
            "title" => "testTitle2"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(0, $response['feeds']['data']);
    }

    /** @test */
    public function index_returns_all_feeds_as_json_filtered_link()
    {
        $filter = [
            "link" => "testLink"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(1, $response['feeds']['data']); // only one a link testLink
        $this->assertEquals('testLink', $response['feeds']['data'][0]['link']);
    }

    /** @test */
    public function index_returns_empty_items_as_json_filtered_link_non_existent()
    {
        $filter = [
            "link" => "testLink2"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(0, $response['feeds']['data']);
    }



    /** @test */
    public function index_returns_all_feeds_as_json_filtered_title_link()
    {
        $filter = [
            "title" => "sameTitle",
            "link" => "sameLink"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(1, $response['feeds']['data']);
        $this->assertEquals('sameLink', $response['feeds']['data'][0]['link']);
        $this->assertEquals('sameTitle', $response['feeds']['data'][0]['title']);
    }

    /** @test */
    public function index_returns_empty_items_as_json_filtered_title_correct_link_incorrect()
    {
        $filter = [
            "title" => "testTitle",
            "link" => "testLink2"
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(0, $response['feeds']['data']);
    }

    /** @test */
    public function index_returns_all_feeds_as_json_filtered_date()
    {
        $filter = [
            "publish_date_from" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 12:12:00"),
            "publish_date_to" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 12:12:00")
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);

        $this->assertCount(1, $response['feeds']['data']); // only one a with that date
        $this->assertEquals("2020-02-02 12:12:00", $response['feeds']['data'][0]['publish_date']);
    }

    /** @test */
    public function index_returns_error_as_json_filtered_date_from_bigger_than_date_to()
    {
        $filter = [
            "publish_date_from" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 15:12:00"),
            "publish_date_to" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 12:12:00")
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals(["publish_date_from" =>[0 => "The publish date from must be a date before or equal to publish date to."], "publish_date_to" => [0 => "The publish date to must be a date after or equal to publish date from."]], $response['message']);
    }

    /** @test */
    public function index_returns_error_as_json_filtered_date_to_smaller_than_date_from()
    {
        $filter = [
            "publish_date_from" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-02 15:12:00"),
            "publish_date_to" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00")
        ];

        $response = $this->call("GET", route('feeds.index'), $filter);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals(["publish_date_from" =>[0 => "The publish date from must be a date before or equal to publish date to."], "publish_date_to" => [0 => "The publish date to must be a date after or equal to publish date from."]], $response['message']);
    }

    ##### STORE #####


    /** @test */
    public function store_creates_new_feed()
    {
        $data = [
            "title" => "successCreateTitle",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['created'], $response['message']);
        $this->assertDatabaseHas('feeds', $data);
    }


    /** @test */
    public function store_fails_missing_title()
    {
        $data = [
            "title" => null,
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The title field is required.", $response['message']['title'][0]);
    }


    /** @test */
    public function store_fails_missing_link()
    {
        $data = [
            "title" => "testTitle2",
            "link" => null,
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The link field is required.", $response['message']['link'][0]);
    }

    /** @test */
    public function store_fails_missing_publish_date()
    {
        $data = [
            "title" => "testTitle2",
            "link" => "testLink2",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => null,
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The publish date field is required.", $response['message']['publish_date'][0]);
    }

    /** @test */
    public function store_fails_invalid_link()
    {
        $data = [
            "title" => "testTitle2",
            "link" => "testLink2",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The link must be a valid URL.", $response['message']['link'][0]);
    }

    /** @test */
    public function store_fails_invalid_source_url()
    {
        $data = [
            "title" => "testTitle2",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "testLink2",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The source url must be a valid URL.", $response['message']['source_url'][0]);
    }

    /** @test */
    public function store_fails_invalid_publish_date()
    {
        $data = [
            "title" => "testTitle2",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => "test",
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The publish date is not a valid date.", $response['message']['publish_date'][0]);
    }

    /** @test */
    public function store_fails_duplicate_title()
    {
        $data = [
            "title" => "testTitle",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("POST", route('feeds.store'), $data);
        $this->assertEquals(RequestConstants::STATUS_CODES['duplicate'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['duplicate'], $response['message']);
    }

    /** @test */
    public function store_fails_duplicate_link()
    {
        $data = [
            "title" => "successCreateTitle2",
            "link" => "http://test2.com",
            "source" => "successCreateSource2",
            "source_url" => "http://test2.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription2"
        ];

        $this->call("POST", route('feeds.store'), $data);

        $dataDuplicate = [
            "title" => "successCreateTitle3",
            "link" => "http://test2.com",
            "source" => "successCreateSource3",
            "source_url" => "http://test3.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription3"
        ];

        $response = $this->call("POST", route('feeds.store'), $dataDuplicate);

        $this->assertEquals(RequestConstants::STATUS_CODES['duplicate'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['duplicate'], $response['message']);
    }

    ##### SHOW ######

    /** @test */
    public function show_success()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];
        $response = $this->call("GET", route('feeds.show', ['id' => $feedId]));

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
    }

    /** @test */
    public function show_fails_not_found()
    {
        $response = $this->call("GET",  route('feeds.show', ['id' => 999]));

        $this->assertEquals(RequestConstants::STATUS_CODES['not_found'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['not_found'], $response['message']);
    }

    ##### DELETE #####

    /** @test */
    public function delete_success()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];
        $response = $this->call("DELETE", route('feeds.delete', ['id' => $feedId]));
        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['deleted'], $response['message']);
    }

    /** @test */
    public function delete_fails_not_found()
    {
        $response = $this->call("DELETE", route('feeds.delete', ['id' => 999]));
        $this->assertEquals(RequestConstants::STATUS_CODES['not_found'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['not_found'], $response['message']);
    }

    ##### UPDATE #####

    /** @test */
    public function update_success()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "updatedName",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['updated'], $response['message']);
        $this->assertDatabaseHas('feeds', $data);
    }

    /** @test */
    public function update_fails_notFound()
    {
        $data = [
            "title" => "updatedName",
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => 9999]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['not_found'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['not_found'], $response['message']);
    }

    /** @test */
    public function update_fails_missing_title()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => null,
            "link" => "http://test.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The title field is required.", $response['message']['title'][0]);
    }

    /** @test */
    public function update_fails_missing_link()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "UpdateTitle",
            "link" => null,
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The link field is required.", $response['message']['link'][0]);
    }

    /** @test */
    public function update_fails_missing_publish_date()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "UpdateTitle",
            "link" => "http://test123.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => null,
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The publish date field is required.", $response['message']['publish_date'][0]);
    }

    /** @test */
    public function update_fails_invalid_link()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "UpdateTitle",
            "link" => "httpa://test123",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The link must be a valid URL.", $response['message']['link'][0]);
    }

    /** @test */
    public function update_fails_invalid_source_url()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "UpdateTitle",
            "link" => "http://test123.com",
            "source" => "successCreateSource",
            "source_url" => "httpsda://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The source url must be a valid URL.", $response['message']['source_url'][0]);
    }

    /** @test */
    public function update_fails_invalid_publish_date()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "UpdateTitle",
            "link" => "http://test123.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => "test",
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['validation'], $response['status']);
        $this->assertEquals("The publish date is not a valid date.", $response['message']['publish_date'][0]);
    }

    /** @test */
    public function update_fails_duplicate_title()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = [
            "title" => "sameTitle",
            "link" => "http://test123.com",
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['duplicate'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['duplicate'], $response['message']);
    }

    /** @test */
    public function update_fails_duplicate_link()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];
        $response = $this->call("GET", route('feeds.index'), []);
        $feedExistingLink = $response['feeds']['data'][3]['link'];

        $data = [
            "title" => "updatedTitle23",
            "link" => $feedExistingLink,
            "source" => "successCreateSource",
            "source_url" => "http://test.com",
            "publish_date" => DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-01 12:12:00"),
            "description" => "successCreateDescription"
        ];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['duplicate'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['duplicate'], $response['message']);
    }

    /** @test */
    public function update_success_no_duplicate_error_when_no_data_change()
    {
        $response = $this->call("GET", route('feeds.index'), []);
        $feedId = $response['feeds']['data'][0]['id'];

        $data = $response['feeds']['data'][0];

        $response = $this->call("PUT", route('feeds.edit', ['id' => $feedId]), $data);

        $this->assertEquals(RequestConstants::STATUS_CODES['success'], $response['status']);
        $this->assertEquals(RequestConstants::RESPONSES['updated'], $response['message']);
    }
}
